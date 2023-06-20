<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('content.chart_title') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6 md:grid md:grid-cols-4 md:gap-2">
        <div class="">
            <div class="h-full bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('content.PET_explanation_title') }}</h2>
                    <div class="font-medium text-sm text-gray-700">
                        <p class="text-justify">{{ __('content.PET_explanation') }}</p>
                        <p>
                            <i>{{ __('content.PET_explanation_source') }}.</i>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="md:col-span-3">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-2 md:mt-0">
                <div class="p-6 text-gray-900">
                    <canvas class="mb-3" id="PET_chart"></canvas>
                </div>
            </div>
            <div class="mt-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="column is-four-fifths-fullhd is-three-quarters-desktop content">
                        <h5 class="has-text-centered"></h5>
                        <section class="is-size-6">
                            <p class="font-medium text-justify text-sm text-gray-700">{{ __('content.chart_explanation') }}
                                <a target="_blank" href="https://www.bpika.hz.nl/">{{ __('content.wiki_link') }}</a>
                            </p>
                            <table class="table-auto w-full">
                                <tr>
                                    <th>{{ __('content.temperature_stress.columns.PET') }}</th>
                                    <th>{{ __('content.temperature_stress.columns.perception') }}</th>
                                    <th>{{ __('content.temperature_stress.columns.stress_level') }}</th>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.0-4.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.0-4.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.0-4.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.4-8.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.4-8.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.4-8.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.8-13.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.8-13.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.8-13.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.13-18.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.13-18.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.13-18.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.18-23.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.18-23.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.18-23.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.23-29.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.23-29.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.23-29.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.29-35.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.29-35.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.29-35.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.35-41.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.35-41.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.35-41.stress_level') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('content.temperature_stress.rows.>41.PET') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.>41.perception') }}</td>
                                    <td>{{ __('content.temperature_stress.rows.>41.stress_level') }}</td>
                                </tr>
                            </table>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white dark:bg-gray-800">
        <div class="w-full mx-auto max-w-screen-xl p-2">
            <ul class="flex flex-wrap items-center mt-2 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
                <li>
                    <img class="w-64" src="{{ asset('img/HUISSTIJL_HZ_LOGO_960x593.jpg/') }}" alt="">
                </li>
                <li>
                    <img class="w-64" src="{{ asset('img/Hanzehogeschool.png/') }}" alt="">
                </li>
                <li>
                    <img class="w-64" src="{{ asset('img/logo-hogeschool-rotterdam.png/') }}" alt="">
                </li>
                <li>
                    <img class="w-64" src="{{ asset('img/vhl_logo_kleur_rgb_voetje.jpg/') }}" alt="">
                </li>
            </ul>
            <div class="align">{{ __('content.copyright') }}</div>

        </div>
    </footer>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@^3"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@^2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@^1"></script>
    <script>

        const chartId = 'PET_chart';
        const stations = @json($stations);

        const ctx = document.getElementById('PET_chart');

        const config = {
            type: 'line',
            data: {},
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            parser: 'M/dd/yyyy H:mm:ss',
                            tooltipFormat: 'HH:mm',
                            unit: 'day'
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: true,
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
                label: data.label, //station.label,
                borderColor: data.chart_color, //station.chart_color,
                data: data.data
            });
            myChart.update();
        }

        stations.forEach(function(station) {
            try {
                let today = new Date();
                let sevenDaysAgo = new Date();
                sevenDaysAgo.setDate(today.getDate() - 7);
                let timeString = sevenDaysAgo.toISOString();
                const url = `/api/stations/${station.code}/measurements?startDate=${timeString}&grouping=hourly&column=pet`;

                fetch(url)
                    .then(response => response.text())
                    .then(text => loadData(JSON.parse(text)));
            } catch (error) {
                console.error(`Download error: ${error.message}`);
            }

        });

        // In order to make this work, the variables below need to be declared in the
        // blade file
        // window.onload = loadData(chartId, stations, options);
    </script>
@endpush
</x-app-layout>
