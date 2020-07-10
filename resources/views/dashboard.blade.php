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
<div class="brand">PET waardes van de afgelope 7 dagen</div>
<div class="container">
    <div class="row">
            <div class="box col-12">
            <canvas id="chart"></canvas>
            </div>
    </div>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="copyright">Copyright &copy; Burger Participatie in Klimaat Adaptatie (BPIKA) 2020</div>
            </div>
        </div>
    </div>
</footer>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script src="{{ asset('js/app.js') }}" defer></script>

</html>
