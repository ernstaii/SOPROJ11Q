<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('stylesheets/error.css') }}">
    <link rel="stylesheet" href="{{asset('stylesheets/configStyle.css')}}">
    @yield('head')
</head>
<body>
<div class="page-top-bar">
    <h1 class="page-top-bar-header">Hunted Spel Configuratie</h1>
</div>
<div class="horizontal-body-box">
    <div class="side-bar">
        <div class="side-bar-item side-bar-elem1">Spelcreatie</div>
        <div class="side-bar-item side-bar-elem2">Spelconfiguratie</div>
        <div class="side-bar-item side-bar-elem3">Speloverzicht</div>
    </div>
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
</div>
</body>
</html>
