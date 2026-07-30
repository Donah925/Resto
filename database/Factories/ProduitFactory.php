<?php

namespace Database\Factories;

use App\Models\Produit;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    private array $plats = [
        ['fr' => 'Garba Premium', 'en' => 'Premium Garba'],
        ['fr' => 'Poulet Braisé (1/2)', 'en' => 'Grilled Chicken (1/2)'],
        ['fr' => 'Attiéké Poisson', 'en' => 'Attiéké with Fish'],
        ['fr' => 'Kedjenou de Poulet', 'en' => 'Kedjenou Chicken'],
        ['fr' => 'Riz Gras au Bœuf', 'en' => 'Beef Jollof Rice'],
        ['fr' => 'Alloco et Poisson', 'en' => 'Alloco and Fish'],
        ['fr' => 'Burger Gourmet', 'en' => 'Gourmet Burger'],
        ['fr' => 'Jus de Bissap Frais', 'en' => 'Fresh Hibiscus Juice'],
        ['fr' => 'Bouillie de Mil', 'en' => 'Millet Porridge'],
    ];

    public function definition(): array
    {
        $plat = $this->faker->randomElement($this->plats);
        $prix = $this->faker->randomElement([1500, 2000, 2500, 3500, 5000, 7500, 10000, 15000]);

        return [
            'categorie_id' => Categorie::inRandomOrder()->first()?->id ?? Categorie::factory(),
            'nom' => json_encode($plat),
            'slug' => str()->slug($plat['fr']),
            'description' => json_encode(['fr' => 'Préparé avec des produits frais et locaux.', 'en' => 'Prepared with fresh and local products.']),
            'prix_base' => $prix,
            'taux_tva' => 18.00,
            'calories' => $this->faker->numberBetween(300, 800),
            'temps_preparation' => $this->faker->randomElement([15, 20, 30, 45]),
            'est_vegetarien' => $this->faker->boolean(20),
            'est_halal' => $this->faker->boolean(80),
            'est_disponible' => true,
            'ordre_tri' => $this->faker->numberBetween(1, 100),
            'cree_le' => now(),
            'modifie_le' => now(),
        ];
    }
}
