<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Monthly billing</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div>
            @endif

            <form method="GET" action="{{ route('admin.billing.index') }}" class="mb-4 flex items-end gap-3">
                <div>
                    <x-input-label for="month_pick" value="Month" />
                    <x-text-input id="month_pick" name="month" type="month" class="block mt-1" :value="$month" required />
                    <x-input-error :messages="$errors->get('month')" class="mt-2" />
                </div>
                <x-secondary-button type="submit">Show</x-secondary-button>
            </form>

            @if ($rows->isEmpty())
                <div class="rounded bg-yellow-100 p-4 text-yellow-800">
                    No rooms are occupied. Add a tenant to a room first.
                </div>
            @else
                <form method="POST" action="{{ route('admin.billing.store') }}">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">

                    @if ($errors->any())
                        <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                            Some values are not valid. Rent, electricity and water must be whole numbers, 0 or more.
                        </div>
                    @endif

                    <div class="mb-4 max-w-xs">
                        <x-input-label for="due_date" value="Due date for this month" />
                        <x-text-input id="due_date" name="due_date" type="date" class="block mt-1 w-full"
                                      :value="old('due_date', $dueDate)" required />
                        <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-4 py-3">Room</th>
                                    <th class="px-4 py-3">Tenant</th>
                                    <th class="px-4 py-3">Rent</th>
                                    <th class="px-4 py-3">Electricity</th>
                                    <th class="px-4 py-3">Water</th>
                                    <th class="px-4 py-3">Total (riel)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($rows as $row)
                                    @php($id = $row['room']->id)
                                    @if ($row['locked'])
                                        <tr class="text-gray-400">
                                            <td class="px-4 py-3 font-medium">{{ $row['room']->room_number }}</td>
                                            <td class="px-4 py-3">{{ $row['tenant']->user->name }}</td>
                                            <td class="px-4 py-3">{{ number_format($row['rent']) }}</td>
                                            <td class="px-4 py-3">{{ number_format($row['electricity']) }}</td>
                                            <td class="px-4 py-3">{{ number_format($row['water']) }}</td>
                                            <td class="px-4 py-3">{{ number_format($row['bill']->total) }} (paid)</td>
                                        </tr>
                                    @else
                                        @php($rent = (int) old("bills.$id.rent", $row['rent']))
                                        @php($electricity = (int) old("bills.$id.electricity", $row['electricity']))
                                        @php($water = (int) old("bills.$id.water", $row['water']))
                                        <tr x-data="{ rent: {{ $rent }}, electricity: {{ $electricity }}, water: {{ $water }} }">
                                            <td class="px-4 py-3 font-medium">{{ $row['room']->room_number }}</td>
                                            <td class="px-4 py-3">{{ $row['tenant']->user->name }}</td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="bills[{{ $id }}][rent]" value="{{ $rent }}"
                                                       x-model.number="rent" min="0" step="1" inputmode="numeric" required
                                                       class="w-28 rounded-md border-gray-300 text-sm">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="bills[{{ $id }}][electricity]" value="{{ $electricity }}"
                                                       x-model.number="electricity" min="0" step="1" inputmode="numeric" required
                                                       class="w-28 rounded-md border-gray-300 text-sm">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="bills[{{ $id }}][water]" value="{{ $water }}"
                                                       x-model.number="water" min="0" step="1" inputmode="numeric" required
                                                       class="w-28 rounded-md border-gray-300 text-sm">
                                            </td>
                                            <td class="px-4 py-3 font-medium"
                                                x-text="(Number(rent) + Number(electricity) + Number(water)).toLocaleString('en-US')">{{ number_format($rent + $electricity + $water) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($rows->contains(fn ($row) => ! $row['locked']))
                        <div class="mt-6">
                            <x-primary-button>Save all bills</x-primary-button>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-gray-500">All bills for this month are paid and locked.</p>
                    @endif
                </form>
            @endif
        </div>
    </div>
</x-app-layout>