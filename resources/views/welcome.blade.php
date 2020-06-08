<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Burger Participatie in Klimaat Adaptatie (BPIKA)</title>

    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/business-casual.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<div class="brand">Burger Participatie in Klimaat Adaptatie (BPIKA)</div>
<div class="container">
    <div class="row">
        <div class="box">
            <div class="col-lg-12 text-center">
                <img class="img-fluid img-full" src="img/office.jpg" alt="">

                <h2 class="brand-before">
                    <small>Welcome to</small>
                </h2>
                <h1 class="brand-name">Burger Participatie in Klimaat Adaptatie (BPIKA)</h1>


                <hr class="tagline-divider">
                <h2>
                    <small>
                        <strong>Onderzoek naar burger participatie in klimaatadaptatie in 5 steden in Nederland</strong>
                    </small>
                </h2>

            </div>
        </div>
    </div>


    <div class="row">
        <div class="box">
            <div class="col-lg-12">
                <p>Een onderzoek naar het veranderende klimaat en burger participatie in klimaat adaptatie.
                    Een samenwerking van Hogeschool Zeeland, Hanzehogeschool Groningen, Hogeschool Rotterdam en
                    Hogeschool Van Hall-Larenstein</p>

            </div>
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
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>

<script type="application/ld+json">
    {
      "@context" : "http://schema.org",
      "@type" : "Organization",
      "name" : "Burger Participatie in Klimaat Adaptatie (BPIKA)",
      "url" : "\/\/bpika.hz.nl",
    }


</script>
</body>
</html>
