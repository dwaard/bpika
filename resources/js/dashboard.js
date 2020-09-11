import * as $ from 'jquery';
import moment from 'moment';

function createChart() {
    const ctx = document.getElementById('chart').getContext('2d');

    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: []
        },
        options: {
            spanGaps:true,
            scales: {
                xAxes: [{
                  type: 'time'
                }]
            }
        }
    });
    return myChart;
}

function dashboard() {
    let myChart = createChart();
    let stations = [
        {
            'name': 'Vredehof-Zuid',
            'code': 'HZ1',
            'color': "#0000ff"
        },
        {
            'name': 'OudeBinnenstad',
            'code': 'HZ4',
            'color': "#41BEAE"
        },
        // {
        //     'name': 'Binnenstad',
        //     'code': 'HZ2',
        //     'color': "#ff6666"
        // },
        {
            'name': 'Magistraatwijk',
            'code': 'HZ3',
            'color': "#ff0000"
        },
        {
            'name': 'Liskwartier',
            'code': 'HSR1',
            'color': "#f4730b"
        },
        {
            'name': 'Bloemhof',
            'code': 'HSR2',
            'color': "#f8ab6d"
        },
        {
            'name': 'Stiens',
            'code': 'VHL1',
            'color': "#00ff00"
        },
        {
            'name': 'Cambuursterpad',
            'code': 'VHL2',
            'color': "#7aff7a"
        },
        {
            'name': 'Paddepoel',
            'code': 'HHG1',
            'color': "#973f73"
        },
    ];
    let today = new Date();
    let sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(today.getDate()-7);
    console.log(sevenDaysAgo);
    let timeString = sevenDaysAgo.toISOString();

    stations.forEach(function(station) {
        let temperatures = [];
        $.ajax({
            url:'/api/measurement/startDate=' + timeString + '&endDate=null&stations=' + station.code + '&grouping=hourly&aggregation=avg&columns=PET&order=desc',
            dataType: 'json'
        }).done((response) => {
            response.measurements.forEach(measurement => {
                temperatures.push({
                    // Subtract 1 from month because of difference in javascript and php data objects
                    x: new Date(measurement.year, (measurement.month-1), measurement.day, measurement.hour),
                    y: measurement['Physiologically Equivalent Temperature [°C]']
                });
            });
            myChart.data.datasets.push({
                label: station.name,
                data: temperatures,
                borderColor: station.color,
                fill: false,
            });
            myChart.update();
        });
    });


}

window.onload = dashboard();
