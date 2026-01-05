<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Categoria;
use Illuminate\Support\Facades\Hash;

class InicializadorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el Administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 2. Crear Categorías de prueba
        $cat1 = Categoria::create(['nombre' => 'Parrilla y Carnes']);
        $cat2 = Categoria::create(['nombre' => 'Bebidas Frías']);

        $this->command->info('Usuario admin: admin@admin.com / admin123');
    }
}