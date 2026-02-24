@php
$classes = 'inline-flex items-center px-4 py-2 bg-saas-blue dark:bg-blue-600 border border-transparent rounded-saas font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-500 active:bg-saas-navy dark:active:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-saas-blue focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-saas';
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
    {{ $slot }}
</button>
