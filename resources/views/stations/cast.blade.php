<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
<main class="flex w-screen bg-gray-100">
    {{-- Center section of the screen --}}
    <div class="grow p-6 flex flex-col">
        <div class="text-8xl text-primary-dark">
            {{ $station->name }} weather
        </div>

        <div class="grow mt-10 text-9xl text-black font-bold p-4 text-right w-full">
            {{ $latest->th_temp }}&deg;C
        </div>

        <div class="p-6 text-gray-900 h-96">
            <canvas id="PET_chart"></canvas>
        </div>

    </div>

    {{-- Right section of the screen --}}
    <div class="flex-none w-1/3 bg-primary p-6 h-screen">
        <div class="grid grid-cols-1">

            <x-tile>
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="128" height="128" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M21.86 12.5A4.313 4.313 0 0 0 19 11c0-1.95-.68-3.6-2.04-4.96C15.6 4.68 13.95 4 12 4c-1.58 0-3 .47-4.25 1.43s-2.08 2.19-2.5 3.72c-1.25.28-2.29.93-3.08 1.95S1 13.28 1 14.58c0 1.51.54 2.8 1.61 3.85C3.69 19.5 5 20 6.5 20h12c1.25 0 2.31-.44 3.19-1.31c.87-.88 1.31-1.94 1.31-3.19c0-1.15-.38-2.15-1.14-3m-1.59 4.77c-.48.49-1.07.73-1.77.73h-12c-.97 0-1.79-.34-2.47-1C3.34 16.29 3 15.47 3 14.5s.34-1.79 1.03-2.47C4.71 11.34 5.53 11 6.5 11H7c0-1.38.5-2.56 1.46-3.54C9.44 6.5 10.62 6 12 6s2.56.5 3.54 1.46C16.5 8.44 17 9.62 17 11v2h1.5c.7 0 1.29.24 1.77.73S21 14.8 21 15.5s-.24 1.29-.73 1.77M8.03 10.45c0-.78.64-1.42 1.42-1.42c.78 0 1.42.64 1.42 1.42c0 .78-.64 1.42-1.42 1.42c-.78 0-1.42-.64-1.42-1.42m7.94 5.1c0 .78-.64 1.42-1.42 1.42c-.78 0-1.42-.64-1.42-1.42c0-.78.64-1.42 1.42-1.42c.78 0 1.42.64 1.42 1.42M14.8 9l1.2 1.2L9.2 17L8 15.8z"/></svg>
                </x-slot:icon>
                {{ $latest->th_hum }}%
            </x-tile>

            <x-tile>
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="128" height="128" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2m0 2a8 8 0 0 1 8 8c0 2.4-1 4.5-2.7 6c-1.4-1.3-3.3-2-5.3-2s-3.8.7-5.3 2C5 16.5 4 14.4 4 12a8 8 0 0 1 8-8m2 1.89c-.38.01-.74.26-.9.65l-1.29 3.23l-.1.23c-.71.13-1.3.6-1.57 1.26c-.41 1.03.09 2.19 1.12 2.6c1.03.41 2.19-.09 2.6-1.12c.26-.66.14-1.42-.29-1.98l.1-.26l1.29-3.21l.01-.03c.2-.51-.05-1.09-.56-1.3c-.13-.05-.26-.07-.41-.07M10 6a1 1 0 0 0-1 1a1 1 0 0 0 1 1a1 1 0 0 0 1-1a1 1 0 0 0-1-1M7 9a1 1 0 0 0-1 1a1 1 0 0 0 1 1a1 1 0 0 0 1-1a1 1 0 0 0-1-1m10 0a1 1 0 0 0-1 1a1 1 0 0 0 1 1a1 1 0 0 0 1-1a1 1 0 0 0-1-1"/></svg>
                </x-slot:icon>
                {{ $latest->thb_press }} hPa
            </x-tile>

            <x-tile>
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="128" height="128" viewBox="0 0 30 30">
                        <g transform="rotate({{ 180 + $latest->wind_dir }} 15 15)"><path fill="currentColor" d="M3.74 14.5c0-2.04.51-3.93 1.52-5.66s2.38-3.1 4.11-4.11s3.61-1.51 5.64-1.51c1.52 0 2.98.3 4.37.89s2.58 1.4 3.59 2.4s1.81 2.2 2.4 3.6s.89 2.85.89 4.39c0 1.52-.3 2.98-.89 4.37s-1.4 2.59-2.4 3.59s-2.2 1.8-3.59 2.39s-2.84.89-4.37.89c-1.53 0-3-.3-4.39-.89s-2.59-1.4-3.6-2.4s-1.8-2.2-2.4-3.58s-.88-2.84-.88-4.37m2.48 0c0 2.37.86 4.43 2.59 6.18c1.73 1.73 3.79 2.59 6.2 2.59c1.58 0 3.05-.39 4.39-1.18s2.42-1.85 3.21-3.2s1.19-2.81 1.19-4.39s-.4-3.05-1.19-4.4s-1.86-2.42-3.21-3.21s-2.81-1.18-4.39-1.18s-3.05.39-4.39 1.18S8.2 8.75 7.4 10.1s-1.18 2.82-1.18 4.4m4.89 5.85l3.75-13.11c.01-.1.06-.15.15-.15s.14.05.15.15l3.74 13.11c.04.11.03.19-.02.25s-.13.06-.24 0l-3.47-1.3c-.1-.04-.2-.04-.29 0l-3.5 1.3c-.1.06-.17.06-.21 0s-.08-.15-.06-.25"/></g></svg>
                </x-slot:icon>
                {{ $latest->wind_avgwind_bft }} BFT
            </x-tile>

        </div>
    </div>
</main>
@livewireScripts
<!--  Other scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@^3"></script>
<script src="https://cdn.jsdelivr.net/npm/luxon@^2"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@^1"></script>
<script>

    const chartId = 'PET_chart';
    const station = '{{ $station->code }}';
    const ctx = document.getElementById('PET_chart');

    const config = {
        type: 'line',
        data: {},
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'time',
                    time: {
                        parser: 'M/dd/yyyy H:mm:ss',
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false,
                    position: 'right',
                    align: 'start'
                },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItem) {
                            return tooltipItem[0].dataset.label;
                        },
                        label: function(tooltipItem) {
                            return tooltipItem.label + " : " + Math.round(tooltipItem.formattedValue * 10) / 10 + "°C";
                        }
                    }
                }
            }
        }
    };

    const myChart = new Chart(ctx, config);

    loadData = function(data) {
        myChart.data.datasets.push({
            label: data.column,
            borderColor: data.column == 'pet' ? '#2ea8db' : '#064e6c',
            data: data.data
        });
        myChart.update();
    }

    addEventListener('load', function() {
        try {
            let today = new Date();
            let start = new Date();
            start.setDate(today.getDate() - 1);
            let timeString = start.toISOString();
            let url = `/api/stations/${station}/measurements?startDate=${timeString}&grouping=hourly&column=th_temp`;
            fetch(url)
                .then(response => response.text())
                .then(text => loadData(JSON.parse(text)));
        } catch (error) {
            console.error(`Download error: ${error.message}`);
        }

    });
</script>
</body>
</html>
