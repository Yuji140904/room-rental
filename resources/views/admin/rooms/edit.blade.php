<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit room {{ $room->room_number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.rooms.update', $room) }}" class="bg-white p-6 shadow-sm sm:rounded-lg">
                @csrf
                @method('PUT')
                @include('admin.rooms._form')
                <div class="mt-6 flex items-center gap-4">
                    <x-primary-button>Save</x-primary-button>
                    <a class="text-sm text-gray-600 underline" href="{{ route('admin.rooms.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>