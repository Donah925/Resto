@props([
    'latitude',
    'longitude',
    'height' => '400px',
    'zoom' => 15,
    'markers' => [],
    'readonly' => false
])

<div x-data="mapComponent({{ $latitude }}, {{ $longitude }}, {{ $zoom }})" 
     x-init="initMap()"
     class="relative rounded-xl overflow-hidden shadow-lg"
     style="height: {{ $height }}">
    
    {{-- Container de la carte --}}
    <div id="map-{{ $attributes['wire:model'] ?? 'map' }}" class="w-full h-full"></div>
    
    @if(!$readonly)
        {{-- Instructions --}}
        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg shadow-md z-[1000]">
            <p class="text-sm text-gray-700">📍 Cliquez sur la carte pour sélectionner un emplacement</p>
        </div>
        
        {{-- Coordonnées --}}
        <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg shadow-md z-[1000]">
            <p class="text-xs font-mono text-gray-600">
                Lat: <span x-text="lat.toFixed(6)"></span> | 
                Lng: <span x-text="lng.toFixed(6)"></span>
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<script>
function mapComponent(initialLat, initialLng, initialZoom) {
    return {
        lat: initialLat,
        lng: initialLng,
        zoom: initialZoom,
        map: null,
        marker: null,
        
        initMap() {
            // Initialiser la carte
            this.map = L.map('map-{{ $attributes['wire:model'] ?? 'map' }}').setView([this.lat, this.lng], this.zoom);
            
            // Ajouter les tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);
            
            @if(!$readonly)
                // Ajouter un marqueur draggable
                this.marker = L.marker([this.lat, this.lng], {
                    draggable: true
                }).addTo(this.map);
                
                // Écouter le dragend
                this.marker.on('dragend', (e) => {
                    const position = e.target.getLatLng();
                    this.lat = position.lat;
                    this.lng = position.lng;
                    
                    // Dispatch Livewire event
                    @this.set('{{ $attributes['wire:model'] ?? 'location' }}', {
                        lat: this.lat,
                        lng: this.lng
                    });
                });
                
                // Cliquer sur la carte
                this.map.on('click', (e) => {
                    this.lat = e.latlng.lat;
                    this.lng = e.latlng.lng;
                    this.marker.setLatLng(e.latlng);
                    
                    @this.set('{{ $attributes['wire:model'] ?? 'location' }}', {
                        lat: this.lat,
                        lng: this.lng
                    });
                });
            @else
                // Mode lecture seule - juste un marqueur
                L.marker([this.lat, this.lng]).addTo(this.map);
            @endif
            
            // Ajouter les markers supplémentaires
            @foreach($markers as $marker)
                L.marker([{{ $marker['lat'] }}, {{ $marker['lng'] }}])
                    @if(isset($marker['popup']))
                        .bindPopup('{{ $marker['popup'] }}')
                    @endif
                    .addTo(this.map);
            @endforeach
        }
    }
}
</script>
@endpush
