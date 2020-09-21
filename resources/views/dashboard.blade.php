@extends('layouts.page')

@section('article')
    <div class="columns">
        <div class="column is-one-fifth-fullhd is-one-quarter-desktop is-full-tablet is-size-6">
            <h2>{{ __('content.PET_explanation_title') }}</h2>
            <p>{{ __('content.PET_explanation') }}</p>
            <p>
                <i>{{ __('content.PET_explanation_source') }}.</i>
            </p>
        </div>
        <div class="column is-four-fifths-fullhd is-three-quarters-desktop is-full-tablet">
            <h1>{{ __('content.chart_title') }}</h1>
            <canvas class="mb-3" id="PET_chart"></canvas>
            <section class="is-size-6">
                <table class="table table-bordered">
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
