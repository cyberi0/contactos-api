<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contacto;
use Faker\Factory as Faker;

class ContactoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5000; $i++) {
            Contacto::create([
                'nombre' => $faker->name
            ]);
        }
    }
}
