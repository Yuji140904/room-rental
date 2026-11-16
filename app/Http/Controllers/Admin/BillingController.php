<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ])['month'] ?? now()->format('Y-m');

        $tenants = Tenant::with(['user', 'room'])
            ->whereNull('move_out_date')
            ->get()
            ->sortBy(fn ($tenant) => $tenant->room->room_number, SORT_NATURAL);

        $roomIds = $tenants->pluck('room_id');

        $existing = Bill::where('month', $month)
            ->whereIn('room_id', $roomIds)
            ->get()
            ->keyBy('room_id');

        $previous = Bill::whereIn('room_id', $roomIds)
            ->where('month', '<', $month)
            ->orderByDesc('month')
            ->get()
            ->unique('room_id')
            ->keyBy('room_id');

        $rows = $tenants->map(function ($tenant) use ($existing, $previous) {
            $bill = $existing->get($tenant->room_id);
            $last = $previous->get($tenant->room_id);

            return [
                'tenant' => $tenant,
                'room' => $tenant->room,
                'bill' => $bill,
                'locked' => $bill?->status === 'paid',
                'rent' => $bill?->rent ?? $last?->rent ?? $tenant->room->default_rent,
                'electricity' => $bill?->electricity ?? $last?->electricity ?? 0,
                'water' => $bill?->water ?? $last?->water ?? 0,
            ];
        });

        $dueDate = $existing->first()?->due_date?->format('Y-m-d')
            ?? Carbon::parse($month.'-01')->day(10)->format('Y-m-d');

        return view('admin.billing.index', compact('month', 'rows', 'dueDate'));
    }

    public function store(Request $request): RedirectResponse
    {
        $money = ['required', 'integer', 'min:0', 'max:100000000'];

        $data = $request->validate([
            'month' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'due_date' => ['required', 'date'],
            'bills' => ['nullable', 'array'],
            'bills.*.rent' => $money,
            'bills.*.electricity' => $money,
            'bills.*.water' => $money,
        ]);

        $month = $data['month'];
        $tenants = Tenant::whereNull('move_out_date')->get()->keyBy('room_id');
        $existing = Bill::where('month', $month)->get()->keyBy('room_id');
        $saved = 0;

        DB::transaction(function () use ($data, $month, $tenants, $existing, &$saved) {
            foreach ($data['bills'] ?? [] as $roomId => $row) {
                $tenant = $tenants->get((int) $roomId);

                if (! $tenant || $existing->get($tenant->room_id)?->status === 'paid') {
                    continue;
                }

                $rent = (int) $row['rent'];
                $electricity = (int) $row['electricity'];
                $water = (int) $row['water'];

                Bill::updateOrCreate(
                    ['room_id' => $tenant->room_id, 'month' => $month],
                    [
                        'tenant_id' => $tenant->id,
                        'rent' => $rent,
                        'electricity' => $electricity,
                        'water' => $water,
                        'total' => $rent + $electricity + $water,
                        'due_date' => $data['due_date'],
                    ]
                );

                $saved++;
            }
        });

        return redirect()
            ->route('admin.billing.index', ['month' => $month])
            ->with('status', "Saved {$saved} bills for {$month}.");
    }
}