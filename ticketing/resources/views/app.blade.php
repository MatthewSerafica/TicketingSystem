<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- link rel="apple-touch-icon" sizes="180x180" href="../../favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../favicon_io/favicon-16x16.png">
    <link rel="manifest" href="../../favicon_io/site.webmanifest"> -->

    <title>TMDD Ticketing System</title>
    <?php echo app('Tightenco\Ziggy\BladeRouteGenerator')->generate(); ?>
    <link rel="icon" type="image/x-icon" href="{{asset('images/favicon.ico')}}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body class="antialiased">
    @inertia
</body>

</html>