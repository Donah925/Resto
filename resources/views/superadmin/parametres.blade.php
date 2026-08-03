@extends('layouts.superadmin')
@section('title', 'Paramètres')
@section('page-title', 'Paramètres de la plateforme')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('superadmin.parametres.update') }}">
        @csrf
        <div class="space-y-4">
            <x-form-input name="nom_plateforme" label="Nom de la plateforme" value="{{ config('app.name') }}" required />
            <x-form-input name="email_support" label="Email support" type="email" value="{{ config('mail.from.address') }}" required />
            <x-form-input name="commission" label="Commission (%)" type="number" step="0.01" value="{{ config('resto.commission', 10) }}" required />
            <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
