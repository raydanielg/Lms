<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class makeFakeFilters extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = App\Models\Category::whereNotNull('parent_id')->get();

        foreach ($categories as $category) {
            foreach (range(1, 4) as $index) {
                DB::table('filters')->insert([
                    'category_id' => $category->id,
                    'title' => Str::random(5) . ' ' . Str::random(6)
                ]);
            }
        }

        $filters = App\Models\Filter::get();
        foreach ($filters as $filter) {
            foreach (range(1, random_int(3, 6)) as $index) {
                DB::table('filter_options')->insert([
                    'filter_id' => $filter->id,
                    'title' => Str::random(3) . ' ' . Str::random(4)
                ]);
            }
        }
    }
}
