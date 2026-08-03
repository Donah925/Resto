@extends('layouts.livreur')
@section('title', 'Véhicule')
@section('page-title', 'Mon Véhicule')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-md">
    <form method="POST" action="{{ route('livreur.vehicule.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <x-form-input name="type" label="Type de véhicule" value="{{ Auth::user()->livreur->vehicule_type ?? '' }}" required />
        <x-form-input name="marque" label="Marque" value="{{ Auth::user()->livreur->vehicule_marque ?? '' }}" />
        <x-form-input name="immatriculation" label="Immatriculation" value="{{ Auth::user()->livreur->vehicule_immat ?? '' }}" required />
        <div>
            <label class="block text-sm font-medium text-gray-700">Photo du véhicule</label>
            <input type="file" name="photo" class="mt-1 block w-full" accept="image/*">
        </div>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">Enregistrer</button>
    </form>
</div>
@endsection
