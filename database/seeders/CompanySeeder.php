<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => Str::random(10),
                'owner_fname' => Str::random(15),
                'email' => Str::random(10).'@gmail.com',
                'address' => Str::random(10).' street USA',
                'website' => Str::random(5).'.com',
                'phone' => '99893'.rand(1000000, 7000000),
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => Str::random(10),
                'owner_fname' => Str::random(15),
                'email' => Str::random(10).'@gmail.com',
                'address' => Str::random(10).' street USA',
                'website' => Str::random(5).'.com',
                'phone' => '99893'.rand(1000000, 7000000),
                'user_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => Str::random(10),
                'owner_fname' => Str::random(15),
                'email' => Str::random(10).'@gmail.com',
                'address' => Str::random(10).' street USA',
                'website' => Str::random(5).'.com',
                'phone' => '99893'.rand(1000000, 7000000),
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => Str::random(10),
                'owner_fname' => Str::random(15),
                'email' => Str::random(10).'@gmail.com',
                'address' => Str::random(10).' street USA',
                'website' => Str::random(5).'.com',
                'phone' => '99893'.rand(1000000, 7000000),
                'user_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
