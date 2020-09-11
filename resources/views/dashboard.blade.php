<!DOCTYPE html>
<html lang="en">
<head>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Visualisation Test</title>
</head>
<body>
<div class="brand">PET waardes van de afgelopen 7 dagen</div>
<div class="container">
    <div class="row">
        <div class="box col-12">
            <canvas class="mb-3" id="chart"></canvas>
            <h3 class="text-center">De kleuren van de lijnen komt overeen met de stad waar het station te vinden is:</h3>
            <ul>
                <li>Vlissingen: <span style="color:blue">blauw</span></li>
                <li>Middelburg: <span style="color:red">rood</span></li>
                <li>Rotterdam: <span style="color:orange">oranje</span></li>
                <li>Leeuwaarden: <span style="color:green">groen</span></li>
                <li>Groningen: <span style="color:purple">paars</span></li>
            </ul>
        </div>
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
