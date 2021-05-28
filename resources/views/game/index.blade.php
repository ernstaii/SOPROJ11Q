@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/mainScreenStyle.css')}}">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="{{asset('scripts/indexScript.js')}}" defer></script>
    <title>.::Webapp Configuration Hunted::.</title>
@endsection


@section('content')
    <div class="main-screen">
        <div class="box shadow">
            <form method="post" action="{{route('games.store')}}" id="game_create_form">
                @csrf
                <label for="name_create_input" id="game_name">Spel naam</label>
                <input type="text" id="game_name" name="name" min="3" placeholder="Vul a.u.b. een naam in..." value="{{old('name')}}">
                <label for="password_create_input" id="password_create_label">Spel wachtwoord</label>
                <div class="password-box">
                    <input type="password" id="password_create_input" name="password" placeholder="Vul a.u.b. een wachtwoord in...">
                    <div>
                        <input type="checkbox" onclick="showPassword('create')">Wachtwoord weergeven
                    </div>
                </div>
                <button class="start-button">Creëer spel</button>
            </form>
            <div class="big-text-box">
                <div class="mini-box shadow">
                    <h2>Vind een bestaand spel</h2>
                    <div class="text-box" id="buttons_box">
                        <label for="get_game_input">Spel naam</label>
                        <div class="tooltip">
                            <span class="tooltiptext"><b class="big-question-mark">?</b>Vul hier de naam van het spel in.</span>
                            <input type="text" id="get_game_input" name="name" placeholder="Vul de naam in">
                        </div>
                        <label id="password_label" for="password_get_input">Spel wachtwoord</label>
                        <div class="password-box">
                            <div class="tooltip">
                                <span class="tooltiptext"><b class="big-question-mark">?</b>Vul hier het wachtwoord van het spel in.</span>
                                <input type="password" id="password_get_input" name="password" placeholder="Vul wachtwoord in">
                            </div>
                            <div id="password_check_box_div">
                                <input type="checkbox" onclick="showPassword('get')">Wachtwoord weergeven
                            </div>
                        </div>
                        <form id="delete_game_form" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            setGameData('{!! json_encode($game_data, JSON_HEX_TAG) !!}');
        });
    </script>
@endsection

