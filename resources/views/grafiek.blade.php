@extends('layouts.grafieklayout')

@section('main')



            <h5 class="has-text-centered">{{ __('content.chart_title') }}</h5>
            <canvas id="PET_chart" class="w-50"></canvas>



@endsection

@push('scripts')
    <script>
        const chartId = 'PET_chart';

        const stations = @json($stations);

        const options = {
            spanGaps:true,
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        unit: 'hour',
                        displayFormats: {
                            hour: 'MMM D H:mm'
                        }
                    },
                    scaleLabel: {
                        display: true,
                        labelString: '{{ __('content.chart_x_axis') }}'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: '{{ __('content.chart_y_axis') }}'
                    }
                }]
            },
            legend: {
                position: 'right'
            }
        };
    </script>
@endpush
