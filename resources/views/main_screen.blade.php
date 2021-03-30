@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/configStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mainScreenStyle.css')}}">
@endsection


@section('content')
    <div class="main-screen">
        <div class="box shadow">
            <form method="post" action="{{route('GoToGame')}}">
                @csrf
                <button class="start-button">Start Game</button>
            </form>
            <div class="big-text-box">
                @foreach($games as $game)
                    <div class="mini-box shadow">
                        <div class="text-box">
                            <a href="{{route('GameScreen', $game->id)}}"><h3>Game {{ $game->id }}</h3></a>
                            <a href="{{route('RemoveGame', $game->id)}}"><h5>Delete Game {{ $game->id }}</h5></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

