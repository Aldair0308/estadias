@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 text-start text-base font-medium text-[var(--primary-text)] dark:text-gray-300 bg-[var(--button-hover-bg)] dark:bg-gray-800 focus:outline-none focus:text-[var(--button-hover-text)] dark:focus:text-white focus:bg-[var(--button-hover-bg)] dark:focus:bg-gray-700 focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[var(--primary-text)] dark:text-gray-300 hover:text-[var(--button-hover-text)] dark:hover:text-white hover:bg-[var(--button-hover-bg)] dark:hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-[var(--button-hover-text)] dark:focus:text-white focus:bg-[var(--button-hover-bg)] dark:focus:bg-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
