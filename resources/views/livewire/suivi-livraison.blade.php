<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Suivi de livraison en temps réel</h3>
    
    @if($livraison && $livraison->statut === 'en_cours')
    <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium">Restaurant → Chez vous</span>
            <span class="text-sm text-gray-500">{{ $eta ?? 'Calcul...' }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-green-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $progress ?? 0 }}%"></div>
        </div>
    </div>
    
    <div id="map" class="h-64 rounded-lg mb-4" wire:ignore></div>
    
    <div class="flex items-center space-x-4">
        <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center text-white">
            {{ substr($livraison->livreur->user->nom ?? 'L', 0, 1) }}
        </div>
        <div>
            <p class="font-semibold">{{ $livraison->livreur->user->nom ?? 'Livreur' }}</p>
            <p class="text-sm text-gray-500">{{ $livraison->livreur->vehicule_type ?? '' }}</p>
        </div>
        <a href="tel:{{ $livraison->livreur->telephone ?? '' }}" class="ml-auto bg-green-600 text-white px-4 py-2 rounded">Appeler</a>
    </div>
    @else
    <x-empty-state message="Aucune livraison en cours" />
    @endif
</div>

@push('scripts')
<script>
let map, marker;
function initMap() {
    map = L.map('map').setView([{{ $lat ?? 48.8566 }}, {{ $lng ?? 2.3522 }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    marker = L.marker([{{ $lat ?? 48.8566 }}, {{ $lng ?? 2.3522 }}]).addTo(map);
}
document.addEventListener('DOMContentLoaded', initMap);
Livewire.on('update-position', (lat, lng) => {
    if (map) { map.setView([lat, lng]); marker.setLatLng([lat, lng]); }
});
</script>
@endpush
