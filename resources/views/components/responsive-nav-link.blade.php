@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#b94245] dark:border-[#b94245] text-start text-base font-medium text-[#8d2a22] dark:text-[#f0c8c6] bg-[#f8efef] dark:bg-[#8d2a22] focus:outline-none focus:text-[#6f201b] dark:focus:text-white focus:bg-[#f4e6e4] dark:focus:bg-[#8d2a22] focus:border-[#8d2a22] dark:focus:border-[#f0c8c6] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
