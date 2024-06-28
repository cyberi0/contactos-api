<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\Contacto;
use App\Models\Direccion;
use Faker\Factory as Faker;

class DireccionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $contactosIds = Contacto::pluck('id')->toArray();

        foreach ($contactosIds as $contactoId) {
            $numDirecciones = rand(1, 3);
            for ($i = 0; $i < $numDirecciones; $i++) {
                Direccion::create([
                    'contacto_id' => $contactoId,
                    'calle' => $faker->streetName,
                    'numero' => $faker->buildingNumber,
                    'codigo_postal' => $faker->postcode,
                ]);
            }
        }
    }
}
