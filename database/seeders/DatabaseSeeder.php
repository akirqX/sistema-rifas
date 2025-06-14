<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Importante!

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cria um usuário de teste que nós controlamos
        User::create([
            'name' => 'AKRX001',
            'email' => 'akrxteste@prodgio.com',
            'password' => Hash::make('teste01'), // Define a senha como 'password'
        ]);

        // Chama o nosso outro seeder para criar a rifa
        $this->call([
            TestDataSeeder::class
        ]);
    }
}