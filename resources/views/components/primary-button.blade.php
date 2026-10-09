<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#b94245] dark:bg-[#b94245] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#8d2a22] dark:hover:bg-[#8d2a22] focus:bg-[#8d2a22] dark:focus:bg-[#8d2a22] active:bg-[#8d2a22] dark:active:bg-[#8d2a22] focus:outline-none focus:ring-2 focus:ring-[#b94245] focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
