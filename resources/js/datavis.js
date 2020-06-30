import * as $ from 'jquery';

function datavis() {
    let stations = ['HZ1'];

    stations.forEach(station => {
        let temps = [];
        let dates = [];

        $.ajax({url: '/api/load/' . station, dataType: 'json'}).done((data) => {
            console.log(data);
            data.measurements.forEach(measurement => {
                if (temps.length < 10) {
                    temps.push(measurement.th_temp);                
                }
                if (dates.length < 10) {
                    dates.push(measurement.created_at);                
                }
            });

            const ctx = document.getElementById(station).getContext('2d');

            const myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Station Data',
                        data: temps,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 3
                    }]
                },
            });
        });
    }); 
}
window.onload = datavis();