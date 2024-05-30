<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('common-user');
        Role::findOrCreate('store-keeper');
    }
}
