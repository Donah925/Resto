@extends('layouts.client')
@section('title', 'Favoris')
@section('page-title', 'Mes Restaurants Favoris')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($favoris as $resto)
    <x-card-restaurant :restaurant="$resto" />
    @empty
    <x-empty-state message="Aucun restaurant en favori" />
    @endforelse
</div>
@endsection
