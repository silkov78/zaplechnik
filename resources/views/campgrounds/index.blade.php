@extends('layouts.app')

@section('content')
    <h1>Campgrounds list</h1>
    @foreach ($campgrounds as $camp)
        <p>Campgrounds {{ $camp->camp_id }}: osm_id - {{ $camp->osm_id }}, {{ $camp->coordinates }}</p>
        @if ($camp->description)
            <p>{{ $camp->description }}</p>
            <br/>
        @endif
    @endforeach
@endsection
