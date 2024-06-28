<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Correo;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CorreoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $contactosIds = Contacto::pluck('id')->toArray();

        foreach ($contactosIds as $contactoId) {
            $numDirecciones = rand(1, 3);
            for ($i = 0; $i < $numDirecciones; $i++) {
                Correo::create([
                    'contacto_id' => $contactoId,
                    'correo' => $faker->email,
                ]);
            }
        }
    }
}
