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
            'name': 'HZ1',
            'color': "#3e95cd"
        },
        {
            'name': 'HZ2',
            'color': "#F2555E"
        },
        {
            'name': 'HZ3',
            'color': "#FFE45E"
        },
        {
            'name': 'HZ4',
            'color': "#41BEAE"
        },
        {
            'name': 'HSR1',
            'color': "#363537"
        },
        {
            'name': 'HSR2',
            'color': "#A882DD"
        },
        {
            'name': 'VHL1',
            'color': "#72B01D"
        },
        {
            'name': 'VHL2',
            'color': "#F3EFF5"
        },
    ];
    let today = new Date();
    let sevenDaysAgo = new Date(today.setDate(today.getDate()-7));
    // Needs to be the full year
    let year = sevenDaysAgo.getFullYear();
    // Needs to range from 01 to 12
    let month = (sevenDaysAgo.getMonth() + 1) < 10 ? '0' + (sevenDaysAgo.getMonth() + 1) : (sevenDaysAgo.getMonth() + 1);
    // Needs to range from 01 to 31
    let day = sevenDaysAgo.getDate() < 10 ? '0' + sevenDaysAgo.getDate() : sevenDaysAgo.getDate();
    // Needs to range from 00 to 23
    let hour = sevenDaysAgo.getHours() < 10 ? '0' + sevenDaysAgo.getHours() : sevenDaysAgo.getHours();
    // Needs to range from 00 to 59
    let minute = sevenDaysAgo.getMinutes() < 10 ? '0' + sevenDaysAgo.getMinutes() : sevenDaysAgo.getMinutes();
    // Needs to range from 00 to 59
    let second = sevenDaysAgo.getSeconds() < 10 ? '0' + sevenDaysAgo.getSeconds() : sevenDaysAgo.getSeconds();
    let timeString = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;

    stations.forEach(function(station) {
        let temperatures = [];
        $.ajax({url:'/api/measurement/startDate=' + timeString + '&endDate=null&stations=' + station.name + '&grouping=hourly&aggregation=avg&columns=PET&order=desc', dataType: 'json'}).done((response) => {
            response.measurements.forEach(measurement => {
                temperatures.push({
                    x: new Date(measurement.year, measurement.month, measurement.day, measurement.hour),
                    y: measurement['Physiologically Equivalent Temperature [°C]']
                });
            });
            myChart.data.datasets.push({
                label: 'Station: ' + station.name,
                data: temperatures,
                borderColor: station.color,
                fill: false,
            });
            myChart.update();
        });
    });


}

window.onload = dashboard();
