<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-primary-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-darkish active:bg-primary-darkish focus:outline-none focus:ring-2 focus:ring-primary-darkish focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
