<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class makeFakeUsers extends Seeder
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

        //'user_id' => factory(App\User::class),

        // make organization
        foreach (range(1, 10) as $index) {
            DB::table('users')->insert([
                'full_name' => $faker->name,
                'email' => $faker->email,
                'password' => '$2y$10$cNBSoHoFGw5.qF3uB7PHgeyamxgSfp4GUI8JVRIOtwsh2KzTtBJWa', // 123456
                'role_name' => 'organization',
                'role_id' => 3,
                'status' => 'active',
                'about' => $faker->paragraph,
                'created_at' => time(),
            ]);
        }

        // make teacher
        foreach (range(1, 10) as $index) {
            DB::table('users')->insert([
                'full_name' => $faker->name,
                'email' => $faker->email,
                'password' => '$2y$10$cNBSoHoFGw5.qF3uB7PHgeyamxgSfp4GUI8JVRIOtwsh2KzTtBJWa', // 123456
                'role_name' => 'teacher',
                'role_id' => 4,
                'status' => 'active',
                'about' => $faker->paragraph,
                'created_at' => time(),
            ]);
        }

        // make teacher for organs
        $organs = \App\User::where('role_name', 'organization')->get();
        foreach ($organs as $organ) {
            foreach (range(1, random_int(2, 6)) as $index) {
                DB::table('users')->insert([
                    'full_name' => $faker->name,
                    'email' => $faker->email,
                    'organ_id' => $organ->id,
                    'password' => '$2y$10$cNBSoHoFGw5.qF3uB7PHgeyamxgSfp4GUI8JVRIOtwsh2KzTtBJWa', // 123456
                    'role_name' => 'teacher',
                    'role_id' => 4,
                    'status' => 'active',
                    'about' => $faker->paragraph,
                    'created_at' => time(),
                ]);
            }
        }
    }
}
