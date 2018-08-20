<?php

use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentsTableSeeder extends Seeder
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
	        DB::table('comments')->insert([
	            'body'       => $faker->paragraph(1),
                'user_id'    => $faker->numberBetween(1, 25),
                'post_id'    => $faker->numberBetween(1, 50),
                'created_at' => "$faker->date $faker->time"
	        ]);
        }

        // votes pivot table
        $users = User::find(range(1, 30));
        $vote = [-1, 1];

        foreach ($users as $user) {
            $commentsIds = [];
            foreach (range(1, 250) as $id) {
                $commentsIds[$id] = [
                    'vote' => $vote[array_rand($vote, 1)]
                ];
            }
            $randCommetnsKeys = (array) array_rand(array_flip(range(1, 250)), rand(1, 15));

            $randComments = [];
            foreach ($randCommetnsKeys as $key) {
                $randComments[$key] = $commentsIds[$key];
            }

            $user->votes()->sync($randComments);
        }
    }
}
