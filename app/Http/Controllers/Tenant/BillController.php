<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Support\Facades\Gate;

class BillController extends Controller
{
    public function show(Bill $bill)
    {
        Gate::authorize('view', $bill);

        return view('tenant.bills.show', compact('bill'));
    }
}