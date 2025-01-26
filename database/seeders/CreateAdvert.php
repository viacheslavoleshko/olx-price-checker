<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateAdvert extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $users = \App\Models\User::all();

        DB::transaction(function () use ($faker, $users) {
            $advert = \App\Models\Advert::create([
                'title' => $faker->sentence(),
                'url' => $faker->bothify('https://www.olx.ua/d/uk/obyavlenie/example-ID*******.html'),
            ]);

            $advert->users()->attach($users->random()->id);
        });
    }
}
