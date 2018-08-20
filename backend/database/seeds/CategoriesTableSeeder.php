<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $id) {
            $title = $faker->unique()->jobTitle;
	        DB::table('categories')->insert([
                'title'        => $title,
                'slug'         => str_slug($title, '-'),
                'description'  => $faker->text(300),
                'moderator'    => $id
	        ]);
        }
    }
}
