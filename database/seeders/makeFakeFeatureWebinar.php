<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\FeatureWebinar;
use Faker\Factory as Faker;

class makeFakeFeatureWebinar extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = FeatureWebinar::$pages;
        $status = ['publish', 'pending'];

        $faker = Faker::create();
        $webinars = \App\Models\Webinar::select('id', 'teacher_id')->get()->random(20);

        foreach ($webinars as $webinar) {
            FeatureWebinar::create([
                'webinar_id' => $webinar->id,
                'page' => $pages[array_rand($pages)],
                'status' => $status[array_rand($status)],
                'updated_at' => $faker->dateTimeBetween('-2 week', 'now')->getTimestamp()
            ]);
        }
    }
}
