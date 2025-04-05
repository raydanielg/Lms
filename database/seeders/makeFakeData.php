<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class makeFakeData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(makeFakeUsers::class);
        $this->call(makeFakeCategories::class);
        $this->call(makeFakeFilters::class);
        $this->call(makeFakeWebinars::class);
        $this->call(makeFakeFeatureWebinar::class);
        $this->call(makeFakeBlog::class);
    }
}
