<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // The tenant's current record (still living there), or the latest one.
        $tenant = Tenant::with('room')
            ->where('user_id', $user->id)
            ->orderByRaw('move_out_date is not null')
            ->latest('id')
            ->first();

        // Only bills linked to this user's own tenant record.
        $bills = Bill::with('room')
            ->whereHas('tenant', fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('month')
            ->get();

        $currentMonth = now()->format('Y-m');
        $current = $bills->firstWhere('month', $currentMonth);
        $earlierUnpaid = $bills->where('status', 'unpaid')->where('month', '<', $currentMonth);

        return view('tenant.dashboard', compact('tenant', 'bills', 'current', 'earlierUnpaid'));
    }
}