<div {{ $attributes->merge(['class' => 'flex items-center gap-2 text-sm']) }}>
    <a href="{{ route('locale', 'en') }}"
       class="{{ app()->getLocale() === 'en' ? 'font-bold text-gray-900' : 'text-gray-500 underline' }}">EN</a>
    <span class="text-gray-300">|</span>
    <a href="{{ route('locale', 'km') }}"
       class="{{ app()->getLocale() === 'km' ? 'font-bold text-gray-900' : 'text-gray-500 underline' }}">ខ្មែរ</a>
</div>