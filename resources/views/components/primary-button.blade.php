<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#F7A923] hover:bg-[#E6940F] border border-transparent rounded-md font-semibold text-xs text-[#2C3E50] uppercase tracking-widest focus:bg-[#E6940F] focus:text-[#2C3E50] active:bg-[#D2840E] focus:outline-none focus:ring-2 focus:ring-[#F7A923] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>
