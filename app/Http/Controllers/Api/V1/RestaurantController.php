<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Http\Resources\V1\RestaurantResource;
use App\Http\Resources\V1\RestaurantCollection;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::query()
            ->actifs()
            ->with(['images', 'services']);

        // Filtres
        if ($request->filled('ville')) {
            $query->where('ville', $request->ville);
        }

        if ($request->boolean('en_avant')) {
            $query->enAvant();
        }

        if ($request->boolean('livraison')) {
            $query->avecLivraison();
        }

        if ($request->boolean('ouverts')) {
            $query->ouvertsMaintenant();
        }

        if ($request->filled('note_min')) {
            $query->where('note', '>=', $request->note_min);
        }

        // Tri
        $sortBy = $request->input('sort', 'note');
        $sortOrder = $request->input('order', 'desc');

        $query->orderBy($sortBy, $sortOrder);

        $restaurants = $query->paginate($request->input('per_page', 20));

        return new RestaurantCollection($restaurants);
    }

    public function proches(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'rayon' => 'nullable|integer|max:50',
        ]);

        $rayon = $request->input('rayon', 10);

        $restaurants = Restaurant::actifs()
            ->prochesDe(
                $request->latitude,
                $request->longitude,
                $rayon
            )
            ->with(['images'])
            ->take(20)
            ->get();

        return RestaurantResource::collection($restaurants);
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->load([
            'images',
            'services',
            'categories.sousCategories',
            'horaires',
        ]);

        return new RestaurantResource($restaurant);
    }

    public function menu(Restaurant $restaurant)
    {
        $categories = $restaurant->categories()
            ->actives()
            ->with([
                'sousCategories' => fn($q) => $q->orderBy('ordre_tri'),
                'produits' => fn($q) => $q->wherePivot('restaurant_id', $restaurant->id)
                    ->wherePivot('est_disponible', true)
                    ->disponibles()
                    ->with(['images', 'variantes', 'supplements', 'allergenes', 'tags'])
                    ->orderBy('ordre_tri'),
            ])
            ->orderBy('ordre_tri')
            ->get();

        return response()->json([
            'restaurant' => new RestaurantResource($restaurant),
            'categories' => $categories,
        ]);
    }

    public function horaires(Restaurant $restaurant)
    {
        return response()->json([
            'horaires' => $restaurant->horaires,
            'fermetures_speciales' => $restaurant->fermeturesSpeciales,
            'est_ouvert' => $restaurant->estOuvertMaintenant(),
        ]);
    }

    public function avis(Restaurant $restaurant, Request $request)
    {
        $avis = $restaurant->avis()
            ->visibles()
            ->with(['client.utilisateur', 'photos'])
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'note_moyenne' => $restaurant->note,
            'total_avis' => $restaurant->total_avis,
            'avis' => $avis,
        ]);
    }
}
