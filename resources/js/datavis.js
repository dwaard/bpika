import * as $ from 'jquery';

async function datavis() {

    let stations = [
        'HZ1',
        'HZ2',
        'HZ3',
        'HZ4',
        'HSR1',
        'HSR2',
        'VHL1',
        'VHL2',
    ];
    let temps = [];
    let dates = [];


    stations.forEach(function(station) {
        let temperatures = [];
        $.ajax({url:'/api/getMeasurement/startdate=NULL&enddate=NULL&format=Y-m-d&station=' + station, dataType: 'json'}).done((response) => {
            response.data.forEach(measurement => {
                if (temperatures.length < 10) {
                    temperatures.push(measurement.th_temp);                
                }
                if (dates.length < 10) {
                    dates.push(measurement.created_at);
                }
            });
        });
        temps.push(temperatures);
    });

    const ctx = document.getElementById('HZ1');

    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Station: ' + stations[0],
                data: temps[0],
                borderColor: "#3e95cd",
                fill: false,
            },
            {
                label: 'Station: ' + stations[1],
                data: temps[1],
                borderColor: "#F2555E",
                fill: false,
            },
            {
                label: 'Station: ' + stations[2],
                data: temps[2],
                borderColor: "#FFE45E",
                fill: false,
            },
            {
                label: 'Station: ' + stations[3],
                data: temps[3],
                borderColor: "#41BEAE",
                fill: false,
            },
            {
                label: 'Station: ' + stations[4],
                data: temps[4],
                borderColor: "#363537",
                fill: false,
            },
            {
                label: 'Station: ' + stations[5],
                data: temps[5],
                borderColor: "#A882DD",
                fill: false,
            },
            {
                label: 'Station: ' + stations[6],
                data: temps[6],
                borderColor: "#72B01D",
                fill: false,
            },
            {
                label: 'Station: ' + stations[7],
                data: temps[7],
                borderColor: "#F3EFF5",
                fill: false,
            }]
            
        },
        options: {
            spanGaps:true,
        }
    });
}

window.onload = datavis();