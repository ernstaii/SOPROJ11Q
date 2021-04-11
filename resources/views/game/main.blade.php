@extends('layout')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Overzicht Game {{$id}}</title>
@endsection

@section('content')
    <h1>Game Screen</h1>
@endsection
