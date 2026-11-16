<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bills</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div>
            @endif

            <form method="GET" action="{{ route('admin.bills.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label for="month" value="Month" />
                    <x-text-input id="month" name="month" type="month" class="block mt-1" :value="$month" />
                    <x-input-error :messages="$errors->get('month')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="block mt-1 border-gray-300 rounded-md shadow-sm">
                        <option value="">All</option>
                        <option value="unpaid" @selected($status === 'unpaid')>Unpaid</option>
                        <option value="paid" @selected($status === 'paid')>Paid</option>
                    </select>
                </div>
                <x-secondary-button type="submit">Filter</x-secondary-button>
                <a href="{{ route('admin.bills.index', ['month' => '']) }}" class="pb-2 text-sm text-gray-600 underline">All months</a>
            </form>

            <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                    <div class="text-sm text-gray-500">Bills</div>
                    <div class="text-xl font-semibold">{{ number_format($summary['count']) }}</div>
                </div>
                <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                    <div class="text-sm text-gray-500">Total (riel)</div>
                    <div class="text-xl font-semibold">{{ number_format($summary['total']) }}</div>
                </div>
                <div class="bg-white p-4 shadow-sm sm:rounded-lg">
                    <div class="text-sm text-gray-500">Unpaid (riel)</div>
                    <div class="text-xl font-semibold">{{ number_format($summary['unpaid']) }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Month</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Tenant</th>
                            <th class="px-4 py-3">Rent</th>
                            <th class="px-4 py-3">Electricity</th>
                            <th class="px-4 py-3">Water</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Due</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($bills as $bill)
                            <tr>
                                <td class="px-4 py-3">{{ $bill->month }}</td>
                                <td class="px-4 py-3 font-medium">{{ $bill->room->room_number }}</td>
                                <td class="px-4 py-3">{{ $bill->tenant?->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ number_format($bill->rent) }}</td>
                                <td class="px-4 py-3">{{ number_format($bill->electricity) }}</td>
                                <td class="px-4 py-3">{{ number_format($bill->water) }}</td>
                                <td class="px-4 py-3 font-medium">{{ number_format($bill->total) }}</td>
                                <td class="px-4 py-3">{{ $bill->due_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    @if ($bill->status === 'paid')
                                        <span class="text-green-700">Paid {{ $bill->paid_at?->format('Y-m-d') }}</span>
                                    @elseif ($bill->due_date->lt(today()))
                                        <span class="font-medium text-red-700">Overdue</span>
                                    @else
                                        <span>Unpaid</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($bill->status === 'paid')
                                        <form method="POST" action="{{ route('admin.bills.unpaid', $bill) }}"
                                              onsubmit="return confirm({{ \Illuminate\Support\Js::from('Mark room '.$bill->room->room_number.' ('.$bill->month.') as UNPAID?') }})">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-gray-600 underline">Mark unpaid</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.bills.paid', $bill) }}"
                                              onsubmit="return confirm({{ \Illuminate\Support\Js::from('Mark room '.$bill->room->room_number.' ('.$bill->month.') as paid?') }})">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-indigo-600 underline">Mark paid</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-4 py-6 text-center text-gray-500">No bills match these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $bills->links() }}</div>
        </div>
    </div>
</x-app-layout>