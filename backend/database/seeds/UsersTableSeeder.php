<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // admin user
        DB::table('users')->insert([
            'email'       => 'admin@admin.com',
            'password'    => bcrypt('111111'),
            'username'    => 'Abdelaziz Sliem',
            'slug'        => str_slug('Abdelaziz Sliem'),
            'privilege'   => 4,
            'description' => 'hello world :)'
        ]);

        $faker = Faker::create();

    	  foreach (range(1, 30) as $id) {
            # define the role of the users
            if ($id < 6)
                $role = 3; #moderator
            elseif ($id < 11)
                $role = 2; #author
            elseif ($id < 26)
                $role = 1; #regular-user
            else
              $role = 0; #banned-user

            $username = $faker->unique()->name;
  	        DB::table('users')->insert([
  	            'email'        => $faker->unique()->email,
                'password'     => bcrypt('111111'),
                'username'     => $username,
                'slug'         => str_slug($username),
                'privilege'    => $role,
                'description'  => $faker->text(300)
  	        ]);
        }
    }
}
