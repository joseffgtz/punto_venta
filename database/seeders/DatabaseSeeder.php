<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrador del Punto de Venta',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cliente@gmail.com'],
            [
                'name' => 'Cliente de Prueba',
                'password' => Hash::make('cliente123'),
                'role' => 'cliente',
            ]
        );

        $products = [
            ['name' => 'Coca-Cola 600 ml', 'description' => 'Refresco individual retornable', 'brand' => 'Coca-Cola', 'price' => 18.00, 'stock' => 25],
            ['name' => 'Sabritas Original', 'description' => 'Papas fritas sabor original', 'brand' => 'Sabritas', 'price' => 17.00, 'stock' => 18],
            ['name' => 'Galletas Marías', 'description' => 'Galletas tradicionales', 'brand' => 'Gamesa', 'price' => 16.50, 'stock' => 22],
            ['name' => 'Jugo Jumex Mango', 'description' => 'Bebida de mango 500 ml', 'brand' => 'Jumex', 'price' => 15.00, 'stock' => 30],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
