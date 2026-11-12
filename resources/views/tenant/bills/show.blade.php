<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bill {{ $bill->month }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-2">
                <div class="flex justify-between"><span>Rent</span><span>{{ number_format($bill->rent) }} riel</span></div>
                <div class="flex justify-between"><span>Electricity</span><span>{{ number_format($bill->electricity) }} riel</span></div>
                <div class="flex justify-between"><span>Water</span><span>{{ number_format($bill->water) }} riel</span></div>
                <div class="flex justify-between border-t pt-2 font-semibold"><span>Total</span><span>{{ number_format($bill->total) }} riel</span></div>
                <div class="flex justify-between text-sm text-gray-600"><span>Due date</span><span>{{ $bill->due_date->format('Y-m-d') }}</span></div>
                <div class="flex justify-between text-sm text-gray-600"><span>Status</span><span>{{ ucfirst($bill->status) }}</span></div>
            </div>
        </div>
    </div>
</x-app-layout>