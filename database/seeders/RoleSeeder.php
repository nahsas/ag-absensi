<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ["id"=>Str::uuid(),"name"=>"pegawai"],
            ["id"=>Str::uuid(),"name"=>"admin"],
            ["id"=>Str::uuid(),"name"=>"superadmin"]
        ]);
    }
}
