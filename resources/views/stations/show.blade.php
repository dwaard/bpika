<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stations') }} > {{ $station->label }}
        </h2>
    </x-slot>

    <x-index-with-actions>
        <x-slot name="actions">
            <x-label>{{ __('Code') }}</x-label>{{ $station->code }}
            <x-label>{{ __('Name') }}</x-label>{{ $station->name }}
            <x-label>{{ __('City') }}</x-label>{{ $station->city }}
            <x-label>{{ __('Timezone') }}</x-label>{{ $station->timezone }}
            <x-label>{{ __('Amount of measurements') }}</x-label>{{ $station->measurements->count() }}
            <x-label>{{ __('Last measurement') }}</x-label>{{ $station->measurements()->latest()->first()->created_at ?? '' }}
            {{-- Divider --}}
            <div class="border-t border-gray-100 w-full"></div>
            <x-primary-button class="my-4"
                onclick="location.href='{{ route('stations.edit', $station) }}';">
                {{ __('Edit this station') }}
            </x-primary-button>
            <br class="pb-4"/>
            @if($station->enabled)
            <form method="POST" action="{{ route('stations.destroy', $station) }}">
                @csrf
                @method('DELETE')
                <x-danger-button type="submit">
                    {{ __('Disable in dashboard') }}
                </x-danger-button>
            </form>
            @else
                <form method="POST" action="{{ route('stations.enable', $station) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="enabled" value="1">
                    <x-secondary-button type="submit">
                        {{ __('Enable in dashboard') }}
                    </x-secondary-button>
                </form>
            @endif
        </x-slot>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <canvas class="mb-3" id="PET_chart"></canvas>
            </div>
        </div>


    </x-index-with-actions>
    @push('scripts')
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
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                parser: 'M/dd/yyyy H:mm',
                                tooltipFormat: 'H:mm',
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
                    label: data.column,
                    borderColor: data.column == 'pet' ? '#2ea8db' : '#064e6c',
                    data: data.data
                });
                myChart.update();
            }

            addEventListener('load', function() {
                try {
                    let today = new Date();
                    let sevenDaysAgo = new Date();
                    sevenDaysAgo.setDate(today.getDate() - 7);
                    let timeString = sevenDaysAgo.toISOString();
                    console.log(timeString);
                    let url = `/api/stations/${station}/measurements?startDate=${timeString}&grouping=hourly&column=th_temp`;

                    fetch(url)
                        .then(response => response.text())
                        .then(text => loadData(JSON.parse(text)));
                    url = `/api/stations/${station}/measurements?startDate=${timeString}&grouping=hourly&column=pet`;

                    fetch(url)
                        .then(response => response.text())
                        .then(text => loadData(JSON.parse(text)));
                } catch (error) {
                    console.error(`Download error: ${error.message}`);
                }

            });
        </script>
    @endpush

</x-app-layout>
