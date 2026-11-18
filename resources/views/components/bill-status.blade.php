@props(['bill'])

@if ($bill->status === 'paid')
    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">{{ __('Paid') }}</span>
@elseif ($bill->due_date->lt(today()))
    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">{{ __('Overdue') }}</span>
@else
    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">{{ __('Unpaid') }}</span>
@endif