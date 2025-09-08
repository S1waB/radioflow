@extends('layouts.app')

@section('title', $emission->name . ' Team')

@section('content')
@include('layouts.header')

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Team for {{ $emission->name }}</h1>
    <ul class="bg-white rounded-lg shadow p-4 space-y-2">
        @foreach($members as $member)
            <li class="p-2 border rounded">{{ $member->name }}</li>
        @endforeach
    </ul>
</div>
@endsection
