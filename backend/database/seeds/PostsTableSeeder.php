<?php

use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $id) {
            // define the post title
            $title = $faker->unique()->text(25);

            // define the user id
            if ($id < 6)
                $authorId = $id;          #moderators
            elseif ($id > 45)
                $authorId = 31;           #admin
            else
                $authorId = $id % 5 + 6;  #authors

            // define the published && published_at
            $published = rand(0, 1);
            $publishedAt = $published ? "$faker->date $faker->time" : null;

	        DB::table('posts')->insert([
	            'title'         => $title,
                'slug'          => str_slug($title, '-'),
                'body'          => $faker->paragraph(18),
                'author_id'     => $authorId,
                'published'     => $published,
                'category_id'   => rand(1, 5),
                'published_at'  => $publishedAt,
                'updated_at'    => $publishedAt
	        ]);
        }

        // recommendations pivot table
        $users = User::find(range(1, 30));
        $postsIds = array_flip(range(1, 50));

        foreach ($users as $user) {
            $randPosts = array_rand($postsIds, rand(1, 7));
            $user->recommendations()->sync($randPosts);
        }
    }
}
