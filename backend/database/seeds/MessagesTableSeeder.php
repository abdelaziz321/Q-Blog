<?php

use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 250) as $id) {
            DB::table('messages')->insert([
                'message'    => $faker->paragraph(1),
                'user_id'    => $faker->numberBetween(1, 6),
                'created_at' => "$faker->date $faker->time"
            ]);
        }
    }
}
