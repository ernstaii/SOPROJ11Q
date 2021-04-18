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
            @if($errors->any())
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            @endif
        </ul>
    </div>
@endif
@yield('content')
</body>
</html>
