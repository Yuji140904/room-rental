<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('room_number')->get();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Room::create($data + ['status' => 'vacant']);

        return redirect()->route('admin.rooms.index')->with('status', 'Room added.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $room->update($this->validated($request, $room));

        return redirect()->route('admin.rooms.index')->with('status', 'Room updated.');
    }

    private function validated(Request $request, ?Room $room = null): array
    {
        return $request->validate([
            'room_number' => [
                'required', 'string', 'max:20',
                Rule::unique('rooms', 'room_number')->ignore($room?->id),
            ],
            'default_rent' => ['required', 'integer', 'min:0', 'max:100000000'],
        ]);
    }
}
