<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees')->insert([
            [
                'passport_pin' => 'AB'.rand(1000000, 7000000),
                'surname' => Str::random(5),
                'name' => Str::random(5).'@gmail.com',
                'middle_name' => Str::random(5).' '.Str::random(5),
                'address' => Str::random(5).' street USA',
                'position' => Str::random(8),
                'phone' => '99893'.rand(1000000, 10000000),
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'passport_pin' => 'AZ'.rand(1000000, 7000000),
                'surname' => Str::random(5),
                'name' => Str::random(5).'@gmail.com',
                'middle_name' => Str::random(5).' '.Str::random(5),
                'address' => Str::random(5).' street USA',
                'position' => Str::random(8),
                'phone' => '99893'.rand(1000000, 10000000),
                'company_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'passport_pin' => 'AZ'.rand(1000000, 7000000),
                'surname' => Str::random(5),
                'name' => Str::random(5).'@gmail.com',
                'middle_name' => Str::random(5).' '.Str::random(5),
                'address' => Str::random(5).' street USA',
                'position' => Str::random(8),
                'phone' => '99893'.rand(1000000, 10000000),
                'company_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'passport_pin' => 'ZZ'.rand(1000000, 7000000),
                'surname' => Str::random(5),
                'name' => Str::random(5).'@gmail.com',
                'middle_name' => Str::random(5).' '.Str::random(5),
                'address' => Str::random(5).' street USA',
                'position' => Str::random(8),
                'phone' => '99893'.rand(1000000, 10000000),
                'company_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
