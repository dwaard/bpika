<!DOCTYPE html>
<html lang="en">
<head>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Visualisation Test</title>
</head>
<body>
     <!--TODO FIX ID FOR EVERY STATION-->
    <canvas id="chart" width="1200" height="600"></canvas>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script src="{{ asset('js/app.js') }}" defer></script>

</html>