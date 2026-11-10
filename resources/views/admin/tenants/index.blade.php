<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tenants</h2>
            <a href="{{ route('admin.tenants.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md text-xs font-semibold text-white uppercase tracking-widest hover:bg-gray-700">
                Add tenant
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
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Moved in</th>
                            <th class="px-4 py-3">Moved out</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($tenants as $tenant)
                            <tr class="{{ $tenant->move_out_date ? 'text-gray-400' : '' }}">
                                <td class="px-4 py-3 font-medium">{{ $tenant->user->name }}</td>
                                <td class="px-4 py-3">{{ $tenant->user->phone }}</td>
                                <td class="px-4 py-3">{{ $tenant->room->room_number }}</td>
                                <td class="px-4 py-3">{{ $tenant->move_in_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $tenant->move_out_date?->format('Y-m-d') ?? 'Living here' }}</td>
                                <td class="px-4 py-3 text-right">
                                    @unless ($tenant->move_out_date)
                                        <form method="POST" action="{{ route('admin.tenants.move-out', $tenant) }}"
                                              onsubmit="return confirm('Move out {{ $tenant->user->name }} from room {{ $tenant->room->room_number }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-red-600 underline">Move out</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No tenants yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>