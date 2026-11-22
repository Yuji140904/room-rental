<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bill') }} {{ \Carbon\Carbon::parse($bill->month.'-01')->translatedFormat('F Y') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-md mx-auto px-4 space-y-4">
            <div class="rounded-2xl bg-white p-5 shadow">
                <div class="flex items-center justify-between">
                    <div class="font-semibold text-gray-800">{{ __('Your room') }} {{ $bill->room->room_number }}</div>
                    <x-bill-status :bill="$bill" />
                </div>

                <dl class="mt-4 space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('Rent') }}</dt>
                        <dd class="font-medium">{{ number_format($bill->rent) }} {{ __('riel') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('Electricity') }}</dt>
                        <dd class="font-medium">{{ number_format($bill->electricity) }} {{ __('riel') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">{{ __('Water') }}</dt>
                        <dd class="font-medium">{{ number_format($bill->water) }} {{ __('riel') }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-3 text-lg">
                        <dt class="font-semibold">{{ __('Total') }}</dt>
                        <dd class="font-bold">{{ number_format($bill->total) }} {{ __('riel') }}</dd>
                    </div>
                </dl>

                <div class="mt-4 flex justify-between text-sm text-gray-600">
                    <span>{{ __('Due date') }}</span>
                    <span class="font-medium">{{ $bill->due_date->translatedFormat('d M Y') }}</span>
                </div>
            </div>

            <a href="{{ route('tenant.dashboard') }}" class="block text-center text-sm text-gray-600 underline">
                {{ __('Back to my bills') }}
            </a>
        </div>
    </div>
</x-app-layout>