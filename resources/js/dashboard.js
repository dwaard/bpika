
function createChart(chartId, options) {
    const ctx = document.getElementById(chartId).getContext('2d');

    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: []
        },
        options: options
    });
    return myChart;
}

function dashboard(chartId, stations, options) {
    let myChart = createChart(chartId, options);
    let today = new Date();
    let sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(today.getDate()-7);
    let timeString = sevenDaysAgo.toISOString();

    stations.forEach(function(station) {
        let temperatures = [];
        $.ajax({
            url:'/api/measurement/startDate=' + timeString + '&endDate=null&stations=' + station.name + '&grouping=hourly&aggregation=avg&columns=PET&order=desc',
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
                label: station.title,
                data: temperatures,
                borderColor: station.color,
                fill: false,
            });
            myChart.update();
        });
    });
};


// In order to make this work, the variables below need to be declared in the
// blade file
window.onload = dashboard(chartId, stations, options);





