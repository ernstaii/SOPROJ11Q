@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/mainScreenStyle.css')}}">

    <script src="{{asset('scripts/indexScript.js')}}" defer></script>
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
                <div class="mini-box shadow">
                    <div class="text-box" id="buttons_box">
                        <input type="number" id="get_game_input" onchange="changeNumberInputs({{$games}})" placeholder="Vul het ID van het spel in...">
                        <form id="delete_game_form" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

