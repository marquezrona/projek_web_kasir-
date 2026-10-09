@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#b94245] dark:focus:border-[#b94245] focus:ring-[#b94245] dark:focus:ring-[#b94245] rounded-md shadow-sm']) }}>
