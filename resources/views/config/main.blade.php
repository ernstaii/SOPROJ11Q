@extends('layout')

@section('head')
    <link rel="stylesheet" href="{{asset('stylesheets/configStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mainGameScreenStyle.css')}}">
    <script src="{{asset('scripts/keyGen.js')}}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="background-box">
        <div class="box shadow">
            <div class="item-header">
                <h2>SPEL CONFIGURATIE</h2>
            </div>
            <div class="item-box">
                <div class="form-box-0" id="form_box">
                    <div class="config-form">
                        <div class="form-col-1">
                            <div class="form-item">
                                <label class="form-label-0" for="amt_time">Tijdslimiet</label>
                                <input name="amt_time" class="input-numeric-0" id="amt_time" type="text">
                            </div>
                            <div class="form-item">
                                <label class="form-label-0" for="area">Gebied</label>
                                <button name="area" id="area">Gebied instellen</button>
                            </div>
                            <div class="form-item">
                                <label class="form-label-0" for="interval">Interval locatieupdates</label>
                                <input name="interval" class="input-numeric-0" id="interval" type="text">
                            </div>
                        </div>
                        <div class="form-col-2">
                            <div class="form-item">
                                <label class="form-label-0" for="colours">Kleurenthema</label>
                                <input name="colours" class="input-numeric-0" id="colours" type="text">
                            </div>
                            <div class="form-item" id="code_input">
                                <label class="form-label-0" for="num_participants">Aantal spelers</label>
                                <input min="1" max="50" name="num_participants" class="input-numeric-0" id="participants_number" type="number">
                            </div>
                            <div class="form-item" id="code_button">
                                <button class="submit-button-0" id="send_number" onclick="generateKey({{$id}})">Genereer codes</button>
                            </div>
                            <div class="form-item" id="ratio_slider">
                                <label class="form-label-0" for="ratio-slider">Ratio Agenten : Boeven</label>
                                <input name="ratio-slider" type="range" min="25" max="75" value="50" class="slider" id="ratio_range">
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
                    @foreach($keys as $key)
                        <div class="key-item">
                            <p id="somekey">{{ $key->value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div id="keys_button_box">
                <button class="keys-share-button" id="copy_button" onclick="performCopyAction()">Kopieer codes</button>
                <button class="keys-share-button" id="print_button" onclick="printKeys()">Print codes</button>
            </div>
        </div>
    </div>
@endsection
