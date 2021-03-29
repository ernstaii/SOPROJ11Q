@extends('layout')

@section('head')
    <script src="{{asset('scripts/keyGen.js')}}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="form-box-0" id="form_box">
        <form>
            @csrf
            <label class="form-label-0" for="num_participants">Aantal deelnemers:</label>
            <input min="1" max="50" name="num_participants" class="input-numeric-0" id="participants_number" type="number">
        </form>
        <button class="submit-button-0" id="send_number" onclick="generateKey()">Genereer Codes</button>
    </div>
    <div class="generic-vert-box-0" id="keys_box">
        @foreach($keys as $key)
            <p id="somekey">{{ $key->value }}</p>
        @endforeach
    </div>
@endsection
