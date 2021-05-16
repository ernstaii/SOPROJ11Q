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
<div class="horizontal-body-box">
    <div class="side-bar">
        <a id="side_bar_link1" href="{{route('games.index')}}"><div class="side-bar-item side-bar-elem1">Spelselectie</div></a>
        <a id="side_bar_link2" href="{{route('games.index')}}"><div class="side-bar-item side-bar-elem2">Spelconfiguratie</div></a>
        <a id="side_bar_link3" href="{{route('games.index')}}"><div class="side-bar-item side-bar-elem3">Speloverzicht</div></a>
    </div>
    @yield('content')
</div>
</body>
</html>
