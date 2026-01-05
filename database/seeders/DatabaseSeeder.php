<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // Crear categorías base para que el menú no salga vacío
    \App\Models\Categoria::create(['nombre' => 'Adicional']);
    \App\Models\Categoria::create(['nombre' => 'Comidas Rapidas']);
    \App\Models\Categoria::create(['nombre' => 'Bebidas']);

    // Crear el usuario administrador inicial
    \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'samirbertel@gmail.com',
        'password' => bcrypt('samirandres'),
        'role' => 'admin'
    ]);
}
}
