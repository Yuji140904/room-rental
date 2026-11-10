<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add tenant</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if ($rooms->isEmpty())
                <div class="rounded bg-yellow-100 p-4 text-yellow-800">
                    There are no vacant rooms. Add a room first, or move a tenant out.
                </div>
            @else
                <form method="POST" action="{{ route('admin.tenants.store') }}" class="bg-white p-6 shadow-sm sm:rounded-lg">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full"
                                      :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="phone" value="Phone (login)" />
                        <x-text-input id="phone" name="phone" type="tel" class="block mt-1 w-full"
                                      :value="old('phone')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" value="Password (at least 8 characters)" />
                        <x-text-input id="password" name="password" type="text" class="block mt-1 w-full"
                                      :value="old('password')" required />
                        <p class="mt-1 text-xs text-gray-500">Tell the tenant this password. You can't see it again later.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="room_id" value="Room" />
                        <select id="room_id" name="room_id" required
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Choose a vacant room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                                    {{ $room->room_number }} ({{ number_format($room->default_rent) }} riel)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('room_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="move_in_date" value="Move-in date" />
                        <x-text-input id="move_in_date" name="move_in_date" type="date" class="block mt-1 w-full"
                                      :value="old('move_in_date', now()->toDateString())" required />
                        <x-input-error :messages="$errors->get('move_in_date')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Save</x-primary-button>
                        <a class="text-sm text-gray-600 underline" href="{{ route('admin.tenants.index') }}">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>