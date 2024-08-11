<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('teachers')->insert([
            'name' => 'Agung Prasetyo',
            'number' => mt_rand(1000, 9999),
            'gender' => 'Laki - Laki',
            'phone' => '6281385931773',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
