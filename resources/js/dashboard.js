
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

function loadData(chartId, stations, options) {

    let myChart = createChart(chartId, options);
    let today = new Date();
    let sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(today.getDate() - 7);
    let timeString = sevenDaysAgo.toISOString();

    // Load the data for each graph and push it to graphs
    // TODO graphs are now loaded in sequence instead of in parallel
    let graphs = [];
    let loadGraphs = async () => {
        for (let station of stations) {
            await new Promise(resolve => setTimeout(() => {

                // Make the ajax call
                $.ajax({
                    url: '/api/measurement/startDate=' + timeString + '&' +
                                            'endDate=null&' +
                                            'stations=' + station.code + '&' +
                                            'grouping=hourly&' +
                                            'aggregation=avg&' +
                                            'columns=PET&' +
                                            'order=desc',
                    dataType: 'json'
                }).done((response) => {

                    // Calculate the PET value for each measurement
                    let measurements = []
                    response.measurements.forEach(measurement => {
                        measurements.push({
                            // Subtract 1 from month because of difference in javascript and php data objects
                            x: new Date(measurement.year, (measurement.month - 1), measurement.day, measurement.hour),
                            y: measurement['Physiologically Equivalent Temperature [°C]']
                        });
                    });

                    // Push the data to graphs
                    graphs.push({
                        'label': station.label,
                        'data': measurements,
                        'borderColor': station.chart_color,
                        'fill': false
                    });
                // Return resolve
                }).always(() => {
                    resolve(resolve);
                });
            }, 0));
        }
    }

    // Wait until graphs are loaded
    loadGraphs().then(() => {

        // Sort graphs
        graphs = graphs.sort(function(a, b) {
            let labelA = a.label.toUpperCase();
            let labelB = b.label.toUpperCase();

            if (labelA < labelB) {
                return -1; //labelA comes first
            }
            if (labelA > labelB) {
                return 1; // labelB comes first
            }
            return 0;  // labels must be equal
        });

        // Update chart
        graphs.forEach((graph) => {
            myChart.data.datasets.push(graph);
        })
        myChart.update();
    });
}

// In order to make this work, the variables below need to be declared in the
// blade file
window.onload = loadData(chartId, stations, options);





