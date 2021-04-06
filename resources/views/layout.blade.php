<!doctype html>
<html lang="en">
<head>
    @yield('head')
    <link rel="stylesheet" href="{{ asset('stylesheets/error.css') }}">
</head>
<body>
@if (session()->has('errors'))
    <div id="error-box">
        <ul>
            @foreach (session()->get('errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@yield('content')
</body>
</html>
