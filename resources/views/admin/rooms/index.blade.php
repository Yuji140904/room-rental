<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rooms</h2>
            <a href="{{ route('admin.rooms.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-gray-700">
                Add room
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Default rent (riel)</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($rooms as $room)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $room->room_number }}</td>
                                <td class="px-4 py-3">{{ number_format($room->default_rent) }}</td>
                                <td class="px-4 py-3">{{ ucfirst($room->status) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a class="text-indigo-600 underline" href="{{ route('admin.rooms.edit', $room) }}">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No rooms yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>