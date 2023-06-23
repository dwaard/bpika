<div {{ $attributes->merge(['class' => 'max-w-7xl mx-auto sm:px-6 lg:px-8 py-12 md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-panel class="p-4">
        {{ $actions }}
    </x-panel>

    <div class="mt-5 md:mt-0 md:col-span-2">
        {{ $slot }}
    </div>
</div>
