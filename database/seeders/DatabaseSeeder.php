<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $faker = Faker::create('id_ID');

        for ($i = 1; $i <= 4; $i++) {
            DB::table('categories')->insert([
                'name' => $faker->name,
                'user_id' => '1'
            ]);
        }

        for ($i = 1; $i <= 20; $i++) {
            DB::table('posts')->insert([
                'title' => $faker->name,
                'content' => 'content',
                'image' => 'image.jpg',
                'user_id' => '1',
                'category_id' => rand(1,4)
            ]);
        }


    }
}
