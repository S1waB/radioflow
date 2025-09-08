@extends('layouts.app')

@section('title', 'Episode ' . $episode->number)

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Episode {{ $episode->number }}</h2>
    <p><strong>Season:</strong> {{ $episode->season->number }}</p>
    <p><strong>Aired On:</strong> {{ $episode->aired_on }} {{ $episode->time }}</p>
    <p><strong>Duration:</strong> {{ $episode->duration_minutes }} mins</p>
    <p><strong>Description:</strong> {{ $episode->description ?? 'N/A' }}</p>

    @if($episode->conducteur_path)
        <p><a href="{{ asset('storage/' . $episode->conducteur_path) }}" class="btn btn-outline mt-2" target="_blank">Download Conducteur</a></p>
    @endif

    <a href="{{ url()->previous() }}" class="btn btn-primary mt-4">Back</a>
</div>
@endsection
