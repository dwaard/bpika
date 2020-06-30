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
    <canvas id="HZ1" width="400" height="400"></canvas>

</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
<script src="{{ asset('js/app.js') }}" defer></script>
</html>