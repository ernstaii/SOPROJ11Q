@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/configStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mainScreenStyle.css')}}">
    <title>.::Webapp Configuration Hunted::.</title>
@endsection


@section('content')
    <div class="main-screen">
        <div class="box shadow">
            <form method="post" action="{{route('games.store')}}">
                @csrf
                <button class="start-button">Creëer spel</button>
            </form>
            <div class="big-text-box">
                @foreach($games as $game)
                    <div class="mini-box shadow">
                        <div class="text-box">
                            <a href="{{route('games.show', [$game])}}"><h3>Ga naar spel {{ $game->id }}</h3></a>
                            <form action="{{route('games.destroy', [$game])}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="config-delete-button" type="submit"><b>Verwijder spel {{ $game->id }}</b></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

