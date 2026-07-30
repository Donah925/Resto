<?php

namespace Database\Seeders;

use App\Models\Allergene;
use Illuminate\Database\Seeder;

class AllergeneSeeder extends Seeder
{
    public function run(): void
    {
        $allergenes = [
            ['fr' => 'Arachides', 'icone' => '🥜'],
            ['fr' => 'Gluten', 'icone' => '🌾'],
            ['fr' => 'Lait / Lactose', 'icone' => '🥛'],
            ['fr' => 'Fruits à coque', 'icone' => '🌰'],
            ['fr' => 'Poisson', 'icone' => '🐟'],
            ['fr' => 'Crustacés', 'icone' => '🦐'],
            ['fr' => 'Œufs', 'icone' => '🥚'],
            ['fr' => 'Soja', 'icone' => '🫘'],
        ];

        foreach ($allergenes as $allergene) {
            Allergene::create([
                'nom' => json_encode($allergene),
                'icone' => $allergene['icone'],
            ]);
        }

        $this->command->info('🥜 Allergènes de référence créés');
    }
}
