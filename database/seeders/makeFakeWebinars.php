<?php
namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Webinar;
use App\Models\WebinarFilterOption;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class makeFakeWebinars extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $teachers = \App\User::where('role_name', 'teacher')->get();


        foreach ($teachers as $teacher) {
            $categories = App\Models\Category::whereNotNull('parent_id')->get()->random(3);

            foreach ($categories as $category) {
                $start_date = $faker->dateTimeBetween('-3 week', 'now')->getTimestamp();

                $webinar = Webinar::create([
                    'teacher_id' => $teacher->id,
                    'creator_id' => (!empty($teacher->organ_id)) ? $teacher->organ_id : $teacher->id,
                    'category_id' => $category->id,
                    'title' => $faker->sentence,
                    'start_date' => $start_date,
                    'duration' => rand(10, 160),
                    'seo_description' => $faker->sentence,
                    'image_cover' => 'https://picsum.photos/id/' . rand(1000, 1050) . '/1600/600',
                    'thumbnail' => 'https://picsum.photos/id/' . rand(1000, 1050) . '/400/300',
                    'video_demo' => null,
                    'description' => $faker->paragraph(6),
                    'capacity' => random_int(1, 30),
                    'price' => random_int(0, 99),
                    'support' => random_int(0, 1),
                    'downloadable' => random_int(0, 1),
                    'partner_instructor' => false,
                    'subscribe' => false,
                    'message_for_reviewer' => true,
                    'status' => Webinar::$active,
                    'created_at' => time(),
                ]);

                $filters = App\Models\Filter::where('category_id', $category->id)->get()->random(2);

                foreach ($filters as $filter) {
                    $filterOptions = App\Models\FilterOption::where('filter_id', $filter->id)->get()->random(3);

                    foreach ($filterOptions as $filterOption) {
                        WebinarFilterOption::create([
                            'webinar_id' => $webinar->id,
                            'filter_option_id' => $filterOption->id
                        ]);
                    }
                }

                foreach (range(1, random_int(2, 4)) as $index) {
                    Tag::create([
                        'webinar_id' => $webinar->id,
                        'title' => $faker->word,
                    ]);
                }
            }
        }
    }
}
