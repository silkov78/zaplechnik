@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h1>Campgrounds list</h1>

        <ol>
            @foreach($campgrounds as $camp)
                <li>{{ $camp->osm_id }}, {{ $camp->name }}, {{ $camp->coordinates }}</li>
            @endforeach
        </ol>
    </div>
@endsection
