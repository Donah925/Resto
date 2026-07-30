<?php

namespace Database\Factories;

use App\Models\ProfilClient;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfilClientFactory extends Factory
{
    protected $model = ProfilClient::class;

    public function definition(): array
    {
        return [
            'utilisateur_id' => Utilisateur::factory()->client(),
            'points_fidelite' => $this->faker->numberBetween(0, 5000),
            'total_depense' => $this->faker->randomFloat(2, 10000, 500000),
            'nombre_commandes' => $this->faker->numberBetween(1, 50),
            'opt_in_newsletter' => $this->faker->boolean(70),
            'opt_in_sms' => $this->faker->boolean(60),
            'code_parrainage' => strtoupper($this->faker->bothify('????####')),
            'cree_le' => now(),
            'modifie_le' => now(),
        ];
    }
}
