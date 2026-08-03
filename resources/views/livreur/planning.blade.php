@extends('layouts.livreur')
@section('title', 'Planning')
@section('page-title', 'Mon Planning de Disponibilité')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('livreur.planning.update') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-7 gap-2">
            @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $i => $jour)
            <div class="text-center">
                <p class="font-semibold mb-2">{{ $jour }}</p>
                <input type="checkbox" name="jours[]" value="{{ $i }}" class="mx-auto" {{ in_array($i, $planning ?? []) ? 'checked' : '' }}>
            </div>
            @endforeach
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">Enregistrer</button>
    </form>
</div>
@endsection
