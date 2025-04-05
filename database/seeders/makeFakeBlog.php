<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class makeFakeBlog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 4) as $index) {
            \App\Models\BlogCategory::create([
                'title' => $faker->sentence(2),
            ]);
        }

        $categories = \App\Models\BlogCategory::all();

        foreach ($categories as $category) {
            foreach (range(1, rand(2, 6)) as $index) {
                \App\Models\Blog::create([
                    'title' => $faker->sentence,
                    'category_id' => $category->id,
                    'author_id' => 1,
                    'image' => '/assets/default/img/webinar/example.png',
                    'description' => $faker->paragraph(6),
                    'content' => implode(' \n ',$faker->paragraphs(rand(3, 6))),
                    'enable_comment' => true,
                    'status' => 'publish',
                    'created_at' => time(),
                ]);
            }
        }


    }
}
