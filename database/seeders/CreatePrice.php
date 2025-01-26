<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreatePrice extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adverts = \App\Models\Advert::all();

        DB::transaction(function () use ($adverts) {
            foreach ($adverts as $advert) {
                \App\Models\Price::create([
                    'advert_id' => $advert->id,
                    'value' => rand(100, 10000),
                    'currency' => 'USD',
                    'negotiable' => rand(0, 1),
                    'trade' => rand(0, 1),
                    'budget' => rand(0, 1),
                ]);
            }
        });
    }
}
