<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Анализатор страниц</title>

    <!-- Scripts -->
    <script src="{{asset('js/bootstrap.min.js')}}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body class="min-vh-100 d-flex flex-column">
    @include('layouts.header')
    <main class="flex-grow-1">
        @yield('content')
    </main>
    @include('layouts.footer')
</body>
</html>
