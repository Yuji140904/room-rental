<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BillListController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'status' => ['nullable', Rule::in(['paid', 'unpaid'])],
        ]);

        // First visit: current month. An empty month box means "all months".
        $month = $request->has('month') ? ($filters['month'] ?? null) : now()->format('Y-m');
        $status = $filters['status'] ?? null;

        $base = Bill::query()
            ->when($month, fn ($q) => $q->where('bills.month', $month))
            ->when($status, fn ($q) => $q->where('bills.status', $status));

        $summary = [
            'count' => (clone $base)->count(),
            'total' => (clone $base)->sum('total'),
            'unpaid' => (clone $base)->where('status', 'unpaid')->sum('total'),
        ];

        $bills = (clone $base)
            ->with(['room', 'tenant.user'])
            ->join('rooms', 'rooms.id', '=', 'bills.room_id')
            ->orderByDesc('bills.month')
            ->orderBy('rooms.room_number')
            ->select('bills.*')
            ->paginate(50)
            ->withQueryString();

        return view('admin.bills.index', compact('bills', 'month', 'status', 'summary'));
    }

    public function markPaid(Bill $bill): RedirectResponse
    {
        if ($bill->status !== 'paid') {
            $bill->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('status', "Room {$bill->room->room_number} ({$bill->month}) marked as paid.");
    }

    public function markUnpaid(Bill $bill): RedirectResponse
    {
        $bill->update(['status' => 'unpaid', 'paid_at' => null]);

        return back()->with('status', "Room {$bill->room->room_number} ({$bill->month}) marked as unpaid.");
    }
}