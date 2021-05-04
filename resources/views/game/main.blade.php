@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/mainGameScreenStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mapStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/gameOverviewStyle.css')}}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
    <script src="{{asset('scripts/mapScript_gameOverview.js')}}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Overzicht Spel {{$id}}</title>
@endsection

@section('content')
    <div class="game-main-screen">
        <div class="box shadow">
            <div class="item-header">
                <h2>Spel Status Beheren</h2>
            </div>
            <div class="button-1">
                <form action="{{route('games.update', ['game' => $id])}}" method="post">
                    <div class="form-item game-form">
                        @csrf
                        @method('PUT')
                        <label for="reason">Geef een reden voor het beëindigen</label>
                        <input type="text" id="reason" name="reason">
                        <button type="input" class="keys-share-button" type="submit" name="state" value="{{\App\Enums\Statuses::Finished}}">Beëindig spel</button>
                    </div>
                </form>
            </div>
            <div class="button-2">
                <form action="{{route('games.update', ['game' => $id])}}" method="post">
                    <div class="form-item game-form">
                        @csrf
                        @method('PUT')
                        <label for="reason">Geef een reden voor het pauzeren</label>
                        <input type="text" id="reason" name="reason">
                        <button type="input" class="keys-share-button" type="submit" name="state" value="{{\App\Enums\Statuses::Paused}}">Pauzeer spel</button>
                    </div>
                </form>
            </div>
            <div class="button-3">
                <form action="{{route('games.update', ['game' => $id])}}" method="post">
                    <div class="form-item game-form">
                        @csrf
                        @method('PUT')
                        <button type="input" class="keys-share-button" type="submit" name="state" value="{{\App\Enums\Statuses::Ongoing}}">Hervat spel</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="mapbox shadow">
            <div id="map">
            </div>
        </div>
        <div class="timer-box shadow">
            <div class="center-box">
                <h2>Totale speelduur: {{$duration}} minuten</h2>
                <h1 class="timer">00:00:00</h1>
                <p class="score-text">Boeven score: {{$thieves_score}}</p>
                <p class="score-text">Politie score: {{$police_score}}</p>
            </div>
        </div>
        <div class="bottom-box shadow">
            <div class="item-header">
                <h2>Spel Notificaties</h2>
            </div>
            <div class="messages">
                @foreach($notifications as $notification)
                    <p>{{$notification->created_at}}: {{$notification->message}}</p>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            @foreach($loot as $loot_item)
                applyLootMarker({{$loot_item->location}}, '{{$loot_item->name}}');
            @endforeach
            @foreach($users as $user)
                applyUserMarker({{$user->location}}, '{{$user->username}}', '{{$user->role}}');
            @endforeach
            updateUserPinsOnChange({{$interval}}, '{{$game_status}}', {{$id}});
            @foreach($border_markers as $border_marker)
                applyExistingMarker({{$border_marker->location}});
            @endforeach
            drawLinesForExistingMarkers();
            handleTimerElement('{{$game_status}}', '{{$time_left}}', '{{$duration}}');
        });
    </script>
@endsection
