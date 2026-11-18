<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My bills') }}</h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-md mx-auto px-4 space-y-4">

            @if (! $tenant)
                <div class="rounded-lg bg-yellow-100 p-4 text-yellow-800">
                    {{ __('Your account is not assigned to a room yet. Please contact the owner.') }}
                </div>
            @else
                <div class="text-center">
                    <div class="text-sm text-gray-500">
                        {{ $tenant->move_out_date ? __('Previous room') : __('Your room') }}
                    </div>
                    <div class="text-4xl font-bold text-gray-800">{{ $tenant->room->room_number }}</div>
                    <div class="text-sm text-gray-500">{{ Auth::user()->name }}</div>
                </div>

                @if ($earlierUnpaid->isNotEmpty())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        {{ __('You have unpaid bills from earlier months:') }}
                        <span class="font-semibold">{{ number_format($earlierUnpaid->sum('total')) }} {{ __('riel') }}</span>
                    </div>
                @endif

                @if ($current)
                    <div class="rounded-2xl bg-white p-5 shadow">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($current->month.'-01')->format('F Y') }}
                            </div>
                            <x-bill-status :bill="$current" />
                        </div>

                        <dl class="mt-4 space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">{{ __('Rent') }}</dt>
                                <dd class="font-medium">{{ number_format($current->rent) }} {{ __('riel') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">{{ __('Electricity') }}</dt>
                                <dd class="font-medium">{{ number_format($current->electricity) }} {{ __('riel') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">{{ __('Water') }}</dt>
                                <dd class="font-medium">{{ number_format($current->water) }} {{ __('riel') }}</dd>
                            </div>
                            <div class="flex justify-between border-t pt-3 text-lg">
                                <dt class="font-semibold">{{ __('Total') }}</dt>
                                <dd class="font-bold">{{ number_format($current->total) }} {{ __('riel') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex justify-between text-sm text-gray-600">
                            <span>{{ __('Due date') }}</span>
                            <span class="font-medium">{{ $current->due_date->format('d M Y') }}</span>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl bg-white p-5 text-center text-gray-600 shadow">
                        {{ __('Your bill for this month is not ready yet.') }}
                    </div>
                @endif

                <div class="rounded-2xl bg-white shadow">
                    <div class="border-b px-5 py-3 font-semibold text-gray-800">{{ __('Bill history') }}</div>
                    <ul class="divide-y">
                        @forelse ($bills as $bill)
                            <li>
                                <a href="{{ route('tenant.bills.show', $bill) }}"
                                   class="flex items-center justify-between px-5 py-3 hover:bg-gray-50">
                                    <div>
                                        <div class="font-medium text-gray-800">
                                            {{ \Carbon\Carbon::parse($bill->month.'-01')->format('F Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ number_format($bill->total) }} {{ __('riel') }}
                                        </div>
                                    </div>
                                    <x-bill-status :bill="$bill" />
                                </a>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-center text-gray-500">{{ __('No bills yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>