<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateArvertUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $adverts = \App\Models\Advert::all();

        DB::transaction(function () use ($users, $adverts) {
            foreach ($users as $user) {
                $user->adverts()->syncWithoutDetaching($adverts->random()->id);
            }
        });
    }
}
