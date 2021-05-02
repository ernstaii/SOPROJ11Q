@extends('layout')

@section('head')
    <title>Overzicht Spel {{$id}}</title>
    <link rel="stylesheet" href="{{asset('stylesheets/gameOverviewStyle.css')}}">
    <link rel="stylesheet" href="{{asset('stylesheets/mainGameScreenStyle.css')}}">
@endsection

@section('content')
    <div class="game-main-screen">
        <div class="game-main-box">
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
        </div>
    </div>
@endsection
