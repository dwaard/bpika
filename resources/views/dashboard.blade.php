@extends('layouts.page')

@section('article')
    <div class="container">
        <div class="columns">
            <div class="column is-one-fifth-fullhd is-one-quarter-desktop is-full-tablet is-size-6">
                <h2>Gevoelstemperatuur volgens de PET-schaal</h2>
                <p>
                    Hitte kan in steden zorgen voor overlast. De maat voor de ‘hittestress’
                    die mensen ervaren wordt in Nederland weergegeven met een index voor
                    gevoelstemperatuur: de PET (Physiological Equivalent Temperature).
                    Hittestress (en ook koudestress) ontstaan als er een onbalans is in van
                    aan- en afvoer van warmte vanuit het menselijk lichaam. Bij de berekening
                    van de PET wordt deze onbalans uitgedrukt in een temperatuurschaal. De
                    meteorologische variabelen die daarbij een rol spelen zijn luchttemperatuur,
                    luchtvochtigheid, windsnelheid en straling (directe en diffuse zonnestraling).
                    Daarnaast spelen de kleding die iemand draagt (kledingisolatie) en de
                    lichamelijke inspanning (metabolisme) een rol. Hoewel de gevoelstemperatuur
                    dus mede afhangt van kleding, lichaamskenmerken en gedrag, wordt bij het
                    berekenen van de PET gevoelstemperatuur vaak uitgegaan van een ‘standaard
                    persoon’ met een specifieke kledingisolatie en metabolisme. PET heeft een
                    temperatuurschaal gelijk aan de luchttemperatuur (hier in Celsius). De PET
                    is per definitie gelijk aan hoe de luchttemperatuur binnenshuis gevoeld wordt
                    waar er geen straling en windinvloeden zijn. De PET kan worden uitgedrukt in
                    klassen van thermische perceptie en het stressniveau.
                </p>
                <p>
                    <i>Bron: RIVM Briefrapport 2019-0008.</i>
                </p>
            </div>
            <div class="column is-four-fifths-fullhd is-three-quarters-desktop is-full-tablet">
                <h1>Gevoelstemperatuur (PET hittestress) in de afgelopen week  in de living labs
                    van het project BPiKA</h1>
                <canvas class="mb-3" id="PET_chart"></canvas>
                <section class="is-size-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>PET (ºC)</th>
                            <th>Perceptie</th>
                            <th>Fysiologisch stressniveau</th>
                        </tr>
                        <tr>
                            <td>0-4</td>
                            <td>Heel koud</td>
                            <td>Extreme koudestress</td>
                        </tr>
                        <tr>
                            <td>4-8</td>
                            <td>Koud</td>
                            <td>Sterke koudestress</td>
                        </tr>
                        <tr>
                            <td>8-13</td>
                            <td>Koel</td>
                            <td>Matige koudestress</td>
                        </tr>
                        <tr>
                            <td>13-18</td>
                            <td>Fris</td>
                            <td>Lichte koudestress</td>
                        </tr>
                        <tr>
                            <td>18-23</td>
                            <td>Comfortabel</td>
                            <td>Geen stress</td>
                        </tr>
                        <tr>
                            <td>23-29</td>
                            <td>Beetje warm</td>
                            <td>Lichte warmtestress</td>
                        </tr>
                        <tr>
                            <td>29-35</td>
                            <td>Warm</td>
                            <td>Matige warmtestress</td>
                        </tr>
                        <tr>
                            <td>35-41</td>
                            <td>Heet</td>
                            <td>Grote warmtestress</td>
                        </tr>
                        <tr>
                            <td>&gt;41</td>
                            <td>Zeer heet</td>
                            <td>Extreme warmtestress</td>
                        </tr>
                    </table>
                </section>
            </div>
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
                        labelString: 'Tijd in uren'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'temperatuur in °C'
                    }
                }]
            },
            legend: {
                position: 'right'
            }
        };
</script>
@endpush
