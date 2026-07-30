<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    private array $noms = [
        ['fr' => 'Le Wafou d\'Abidjan', 'en' => 'The Wafou of Abidjan'],
        ['fr' => 'Maquis Chez Tantine', 'en' => 'Auntie\'s Maquis'],
        ['fr' => 'Braise & Saveurs', 'en' => 'Grill & Flavors'],
        ['fr' => 'Le Toit d\'Abidjan', 'en' => 'The Roof of Abidjan'],
        ['fr' => 'Attiéké Poisson Royal', 'en' => 'Royal Fish Attiéké'],
    ];

    private array $quartiers = ['Cocody', 'Marcory', 'Plateau', 'Yopougon', 'Deux Plateaux', 'Angré', 'Riviera'];
    private array $adresses = ['Rue des Jardins', 'Boulevard de la République', 'Avenue Chardy', 'Rue du Commerce', 'Boulevard Valéry Giscard d\'Estaing'];

    public function definition(): array
    {
        $nom = $this->faker->randomElement($this->noms);
        $quartier = $this->faker->randomElement($this->quartiers);
        $rue = $this->faker->randomElement($this->adresses);

        // Coordonnées approximatives d'Abidjan
        $latitude = $this->faker->randomFloat(6, 5.25, 5.40);
        $longitude = $this->faker->randomFloat(6, -4.10, -3.90);

        return [
            'nom' => json_encode($nom),
            'slug' => str()->slug($nom['fr']),
            'description' => json_encode(['fr' => 'Le meilleur de la gastronomie locale et internationale.', 'en' => 'The best of local and international gastronomy.']),
            'telephone' => '+225 07 ' . $this->faker->numerify('## ## ## ##'),
            'email' => 'contact@' . str()->slug($nom['fr']) . '.ci',
            'adresse' => $rue . ', ' . $quartier,
            'ville' => 'Abidjan',
            'code_postal' => '00225',
            'pays' => 'Côte d\'Ivoire',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'devise' => 'XOF',
            'taux_tva' => 18.00,
            'livraison_activee' => true,
            'retrait_active' => true,
            'sur_place_active' => true,
            'montant_minimum_commande' => $this->faker->randomElement([3000, 5000, 10000]),
            'rayon_max_livraison' => 15,
            'temps_preparation' => 30,
            'statut' => 'actif',
            'cree_le' => now(),
            'modifie_le' => now(),
        ];
    }
}
