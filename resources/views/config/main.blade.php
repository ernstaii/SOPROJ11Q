@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/configStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mainGameScreenStyle.css')}}">
    <script src="{{asset('scripts/keyGen.js')}}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configuratie Spel {{$id}}</title>
@endsection

@section('content')
    <div class="background-box">
        <div class="box shadow">
            <div class="item-header">
                <h2>SPEL CONFIGURATIE</h2>
            </div>
            <div class="item-box">
                <div id="form_box">
                    <div class="config-form">
                        <form class="form-col-1" action="/games/{{$id}}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="form-item">
                                <label class="form-label-0" for="duration">Tijdslimiet</label>
                                <input name="duration" class="input-numeric-0" id="duration" type="text">
                            </div>
                            <div class="form-item">
                                <label class="form-label-0" for="interval">Interval locatieupdates</label>
                                <input name="interval" class="input-numeric-0" id="interval" type="text">
                            </div>
                            <div class="form-item">
                                <button type="input" class="keys-share-button" type="submit" name="state"
                                        value="{{\App\Enums\Statuses::Ongoing}}">Start spel
                                </button>
                            </div>
                        </form>
                        <div class="form-col-2">
                            <div class="form-item" id="code_input">
                                <label class="form-label-0" for="num_participants">Aantal spelers</label>
                                <input min="1" max="50" name="num_participants" class="input-numeric-0"
                                       id="participants_number" type="number">
                            </div>
                            <div class="form-item" id="code_button">
                                <button class="submit-button-0" id="send_number" onclick="generateKey({{$id}})">Genereer
                                    codes
                                </button>
                            </div>
                            <div class="form-item" id="ratio_slider">
                                <label class="form-label-0" for="ratio-slider">Ratio Agenten : Boeven</label>
                                <input name="ratio-slider" type="range" min="25" max="75" value="50" class="slider"
                                       id="ratio_range">
                                <div class="slider-labels">
                                    <p>25%</p>
                                    <p>50%</p>
                                    <p>75%</p>
                                </div>
                                <div class="slider-labels">
                                    <p>Minder agenten</p>
                                    <p>Meer agenten</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box shadow">
            <div class="item-header">
                <h2>CODES</h2>
            </div>
            <div class="item-box">
                <div id="keys_box" class="keys-box">
                    <div class="key-item">
                        <h3><b>Politie</b></h3>
                    </div>
                    <div class="key-item">
                        <h3><b>Boeven</b></h3>
                    </div>
                    <div id="police_keys_box" class="vert-keys-box">
                        @foreach($police_keys as $key)
                            <div class="key-item">
                                <p id="somekey">{{ $key->value }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div id="thieves_keys_box" class="vert-keys-box">
                        @foreach($thieves_keys as $key)
                            <div class="key-item">
                                <p id="somekey">{{ $key->value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div id="keys_button_box">
                <button class="generic-button" id="copy_button_agents" onclick="performCopyAction('agent')">Kopieer politie codes</button>
                <button class="generic-button" id="copy_button_thiefs" onclick="performCopyAction('thief')">Kopieer boeven codes</button>
                <button class="generic-button" id="print_button" onclick="printKeys()">Print codes</button>
            </div>
        </div>
    </div>
@endsection
