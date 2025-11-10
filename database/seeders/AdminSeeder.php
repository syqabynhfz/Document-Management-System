<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{

    public function run(): void
    {
         Admin::create([
            'full_name' => 'Fatur Admin',
            'email' => 'admin@rimba.com',
            'password' => '123456' 
        ]);
    }
}