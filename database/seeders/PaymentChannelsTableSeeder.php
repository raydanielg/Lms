<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \App\Models\PaymentChannel;

class PaymentChannelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (\App\Models\PaymentChannel::$classes as $index => $class) {

            $check = PaymentChannel::query()->where('class_name', $class)->first();

            if (empty($check)) {
                PaymentChannel::query()->create([
                    'title' => $class,
                    'class_name' => $class,
                    'status' => 'active',
                    'image' => null,
                    'credentials' => null,
                    'currencies' => null,
                    'created_at' => time()
                ]);
            }
        }
    }
}
