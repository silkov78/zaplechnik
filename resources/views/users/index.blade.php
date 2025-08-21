@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h1>Users list</h1>

        <ol>
            @foreach($users as $user)
                <li>{{ $user->name }}, {{ $user->email }}</li>
            @endforeach
        </ol>
    </div>
@endsection
