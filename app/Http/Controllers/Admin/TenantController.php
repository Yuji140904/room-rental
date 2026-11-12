<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['user', 'room'])
            ->orderByRaw('move_out_date is not null')
            ->latest('id')
            ->get();

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'vacant')->orderBy('room_number')->get();

        return view('admin.tenants.create', compact('rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'regex:/^[0-9]{8,12}$/', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'room_id' => ['required', Rule::exists('rooms', 'id')->where('status', 'vacant')],
            'move_in_date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($data) {
            $room = Room::whereKey($data['room_id'])->lockForUpdate()->first();

            if ($room->status !== 'vacant') {
                throw ValidationException::withMessages([
                    'room_id' => 'This room is no longer vacant.',
                ]);
            }

            $user = new User([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => $data['password'],
            ]);
            $user->role = 'tenant';
            $user->save();

            Tenant::create([
                'user_id' => $user->id,
                'room_id' => $room->id,
                'move_in_date' => $data['move_in_date'],
            ]);

            $room->update(['status' => 'occupied']);
        });

        return redirect()->route('admin.tenants.index')->with('status', 'Tenant created.');
    }

    public function moveOut(Tenant $tenant): RedirectResponse
    {
        if ($tenant->move_out_date) {
            return redirect()->route('admin.tenants.index')->with('status', 'This tenant already moved out.');
        }

        DB::transaction(function () use ($tenant) {
            $tenant->update(['move_out_date' => now()->toDateString()]);
            $tenant->room->update(['status' => 'vacant']);
        });

        return redirect()->route('admin.tenants.index')->with('status', 'Tenant moved out. Room is vacant.');
    }
}
