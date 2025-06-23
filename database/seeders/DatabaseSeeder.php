<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
// Não precisamos mais do 'use Illuminate\Support\Facades\Hash;' aqui se só for usado para isso
// use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cria um usuário de teste que nós controlamos
        User::create([
            'name' => 'AKRX001',
            'email' => 'akrxteste@prodgio.com',
            'is_admin' => true, // <-- Adicionei isso, já que o usuário de teste deve ser admin
            'password' => 'teste01', // Passando a senha como texto puro. O modelo vai criptografar!
        ]);

        // Chama o nosso outro seeder para criar a rifa
        $this->call([
            TestDataSeeder::class
        ]);
    }
}
