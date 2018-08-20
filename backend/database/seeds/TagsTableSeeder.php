<?php

use App\Post;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $id) {
            # define the tag name
            $name = $faker->unique()->city;

            DB::table('tags')->insert([
                'name'  => $name,
                'slug'  => str_slug($name, '-')
            ]);
        }

        // posts_tags pivot table
        $posts = Post::find(range(1, 50));
        $tagsIds = array_flip(range(1, 10));

        foreach ($posts as $post) {
            $randTags = array_rand($tagsIds, rand(1, 7));
            $post->tags()->sync($randTags);
        }
    }
}
