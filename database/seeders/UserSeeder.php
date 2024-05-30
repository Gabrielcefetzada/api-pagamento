<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name'  => 'Gabriel Admin',
            'email' => 'admin@example.com',
            'cpf'   => '15148677619',
            'cnpj'  => null
        ])->assignRole('admin');

        User::factory()->create([
            'name'  => 'Luísa Lojista',
            'email' => 'storekeeper@example.com',
            'cpf'   => '59335481033',
            'cnpj'  => '69883236000110'
        ])->assignRole('store-keeper');
        ;

        User::factory()->create([
            'name'  => 'Jolene Usuario Comum',
            'email' => 'commonuser@example.com',
            'cpf'   => '89853444046',
            'cnpj'  => null
        ])->assignRole('common-user');
        ;
    }
}
