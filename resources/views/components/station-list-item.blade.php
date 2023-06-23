@props(['station'])

@php
    $bg = $station->enabled ? 'bg-white' : 'bg-gray-50';
    $tc = $station->enabled ? '' : 'text-gray-400';

@endphp

<div {!! $attributes->merge(['class' => 'mb-4 max-w-7xl']) !!}
     onclick="location.href='{{ route('stations.show', $station) }}';" style="cursor: pointer;">
    <div class="{{ $bg }} overflow-hidden shadow-xl sm:rounded-lg hover:bg-gray-50">
        <div class="flex items-center gap-4 pr-2">
            <div class="flex-none p-2 m-2 rounded-full" style="background: {{ $station->chart_color }}">
                {{ $station->code }}
            </div>

            <div class="flex-auto grow p-2 {{ $tc }}">
                {{ $station->label }}
                <br>
                <span class="text-xs">{{ $station->timezone }}</span>
            </div>

            <div class="flex-none p-2">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

