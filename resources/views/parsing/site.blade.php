@extends('layout.base')

@section('content')
    <div class="container">
        <h2>Парсинг сайта
        </h2>
        <livewire:parsing-link-form :parsingLink="$parsingLink"/>
    </div>
@endsection