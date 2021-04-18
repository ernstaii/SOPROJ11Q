@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{secure_asset('stylesheets/configStyle.css')}}">
    <link rel="stylesheet" href="{{secure_asset('stylesheets/mainScreenStyle.css')}}">
    <title>.::Webapp Configuration Hunted::.</title>
@endsection


@section('content')
    <div class="main-screen">
        <div class="box shadow">
            <form method="post" action="{{route('GoToGame')}}">
                @csrf
                <button class="start-button">Creëer spel</button>
            </form>
            <div class="big-text-box">
                @foreach($games as $game)
                    <div class="mini-box shadow">
                        <div class="text-box">
                            <a href="{{route('GameScreen', $game->id)}}"><h3>Ga naar spel {{ $game->id }}</h3></a>
                            <a href="{{route('RemoveGame', $game->id)}}"><h5>Verwijder spel {{ $game->id }}</h5></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

