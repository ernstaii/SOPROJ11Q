@extends('layout')

@section('head')
    <title>Overzicht Game {{$id}}</title>
@endsection

@section('content')
    <h1>Game Screen</h1>
    <div class="button-2">
        <form action="/games/{{$id}}" method="post">
            <div class="form-item">
                @csrf
                @method('PUT')
                <button type="input" class="keys-share-button" type="submit" name="state" value="{{\App\Enums\Statuses::Paused}}}">Pauzeer spel</button>
            </div>
        </form>
    </div>
    <div class="button-3">
        <form action="/games/{{$id}}" method="post">
            <div class="form-item">
                @csrf
                @method('PUT')
                <button type="input" class="keys-share-button" type="submit" name="state" value="{{\App\Enums\Statuses::Ongoing}}}">Hervat spel</button>
            </div>
        </form>
    </div>
@endsection
