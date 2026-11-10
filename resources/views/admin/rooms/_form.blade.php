<div>
    <x-input-label for="room_number" value="Room number" />
    <x-text-input id="room_number" name="room_number" type="text" class="block mt-1 w-full"
                  :value="old('room_number', $room->room_number ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="default_rent" value="Default rent (riel)" />
    <x-text-input id="default_rent" name="default_rent" type="number" min="0" step="1" class="block mt-1 w-full"
                  :value="old('default_rent', $room->default_rent ?? '')" required />
    <x-input-error :messages="$errors->get('default_rent')" class="mt-2" />
</div>