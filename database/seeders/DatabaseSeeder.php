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
        // User::factory(10)->create();

        // Criar Admin Organizador
        $admin = User::create([
            'name' => 'Alceu RR Admin',
            'email' => 'admin@rrveiculos.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin_organizador',
            'cpf' => '123.456.789-00',
            'phone' => '(11) 99999-9999',
        ]);

        // Criar Cliente
        $cliente = User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@gmail.com',
            'password' => bcrypt('cliente123'),
            'role' => 'cliente',
            'cpf' => '987.654.321-11',
            'phone' => '(11) 98888-8888',
        ]);

        // Criar Rifa 1 (Gol Turbo)
        \App\Models\Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Gol Quadrado 1.8 AP Turbo',
            'description' => 'Carro impecável, motor AP turbo forjado, rodas orbital aro 16, legalizado e pronto para rodar. Envio para todo o Brasil!',
            'price' => 10.00,
            'total_numbers' => 100,
            'status' => 'active',
            'prize_name' => 'Gol Quadrado 1.8 AP Turbo 1994',
            'prize_description' => 'Gol Quadrado na cor cinza chumbo, bancos Recaro originais, instrumentação ODG, pneus novos.',
            'image_url' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80',
            'draw_date' => now()->addDays(30),
        ]);

        // Criar Rifa 2 (Saveiro Cross)
        \App\Models\Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Saveiro Cross Rebaixada',
            'description' => 'Saveiro Cross completa, suspensão a ar legalizada, rodas aro 18, som interno potente, documentação em dia.',
            'price' => 15.00,
            'total_numbers' => 200,
            'status' => 'active',
            'prize_name' => 'Saveiro Cross 2015 Rebaixada',
            'prize_description' => 'Saveiro na cor branca, bancos de couro, faróis de LED, capota marítima nova.',
            'image_url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80',
            'draw_date' => now()->addDays(45),
        ]);
    }
}
