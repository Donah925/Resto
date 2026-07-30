<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\Allergene;
use Illuminate\Database\Seeder;

class RestaurantMenuSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [
            [
                'nom' => ['fr' => 'Maquis Chez Tantine', 'en' => 'Auntie\'s Maquis'],
                'slug' => 'maquis-chez-tantine',
                'quartier' => 'Yopougon',
                'categories' => [
                    'Plats de Résistance' => ['Garba Premium', 'Attiéké Poisson', 'Kedjenou de Poulet', 'Riz Gras au Bœuf'],
                    'Accompagnements' => ['Alloco', 'Igname Pilée', 'Frites de Plantain'],
                    'Boissons' => ['Jus de Bissap', 'Jus de Gingembre', 'Bière Flag', 'Eau Minérale']
                ]
            ],
            [
                'nom' => ['fr' => 'Braise & Saveurs', 'en' => 'Grill & Flavors'],
                'slug' => 'braise-et-saveurs',
                'quartier' => 'Cocody',
                'categories' => [
                    'Grillades' => ['Poulet Braisé (1/2)', 'Poulet Braisé (Entier)', 'Brochettes de Bœuf', 'Poisson Braisé'],
                    'Burgers & Sandwichs' => ['Burger Gourmet', 'Sandwich Poulet', 'Wrap Végétarien'],
                    'Desserts' => ['Glace Artisanale', 'Tarte aux Fruits', 'Crème Caramel']
                ]
            ]
        ];

        foreach ($restaurants as $data) {
            $restaurant = Restaurant::factory()->create([
                'nom' => json_encode($data['nom']),
                'slug' => $data['slug'],
                'ville' => 'Abidjan',
                'adresse' => $data['quartier'] . ', Abidjan',
            ]);

            $this->command->info("🍽️ Restaurant créé : {$data['nom']['fr']}");

            foreach ($data['categories'] as $nomCategorie => $plats) {
                $categorie = Categorie::create([
                    'restaurant_id' => $restaurant->id,
                    'nom' => json_encode(['fr' => $nomCategorie, 'en' => $nomCategorie]),
                    'slug' => str()->slug($nomCategorie),
                    'est_active' => true,
                ]);

                foreach ($plats as $plat) {
                    $produit = Produit::factory()->create([
                        'categorie_id' => $categorie->id,
                        'nom' => json_encode(['fr' => $plat, 'en' => $plat]),
                        'slug' => str()->slug($plat),
                    ]);

                    // Lier le produit au restaurant avec un prix spécifique
                    $restaurant->produits()->attach($produit->id, [
                        'prix' => $produit->prix_base,
                        'est_disponible' => true,
                    ]);
                }
            }
        }
    }
}
