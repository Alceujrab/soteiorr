<?php

namespace Database\Seeders;

use App\Models\Raffle;
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

        // Outros perfis do PRD
        User::create([
            'name' => 'Super Admin Geral',
            'email' => 'superadmin@rrveiculos.com',
            'password' => bcrypt('admin123'),
            'role' => 'super_admin',
            'cpf' => '000.000.000-01',
            'phone' => '(11) 99999-0001',
        ]);

        User::create([
            'name' => 'Gerente Operacional',
            'email' => 'gerente@rrveiculos.com',
            'password' => bcrypt('admin123'),
            'role' => 'gerente_operacional',
            'cpf' => '000.000.000-02',
            'phone' => '(11) 99999-0002',
        ]);

        User::create([
            'name' => 'Vendedor 1',
            'email' => 'vendedor@rrveiculos.com',
            'password' => bcrypt('admin123'),
            'role' => 'vendedor',
            'cpf' => '000.000.000-03',
            'phone' => '(11) 99999-0003',
        ]);

        User::create([
            'name' => 'Financeiro Diretor',
            'email' => 'financeiro@rrveiculos.com',
            'password' => bcrypt('admin123'),
            'role' => 'financeiro',
            'cpf' => '000.000.000-04',
            'phone' => '(11) 99999-0004',
        ]);

        User::create([
            'name' => 'Suporte Técnico',
            'email' => 'suporte@rrveiculos.com',
            'password' => bcrypt('admin123'),
            'role' => 'suporte',
            'cpf' => '000.000.000-05',
            'phone' => '(11) 99999-0005',
        ]);

        User::create([
            'name' => 'Auditor Externo',
            'email' => 'auditor@rrveiculos.com',
            'password' => bcrypt('admin123'),
            'role' => 'auditor',
            'cpf' => '000.000.000-06',
            'phone' => '(11) 99999-0006',
        ]);

        // Criar Ação Promocional 1 (Gol Turbo)
        $raffle1 = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Gol Quadrado 1.8 AP Turbo',
            'description' => 'Carro impecável, motor AP turbo forjado, rodas orbital aro 16, legalizado e pronto para rodar. Envio para todo o Brasil!',
            'price' => 9.90,
            'total_numbers' => 200000,
            'status' => 'active',
            'prize_name' => 'Gol Quadrado 1.8 AP Turbo 1994',
            'prize_description' => 'Gol Quadrado na cor cinza chumbo, bancos Recaro originais, instrumentação ODG, pneus novos.',
            'image_url' => 'https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=800&q=80',
            'draw_date' => now()->addDays(30),
        ]);
        $raffle1->seedDefaultPackages();

        // Criar Ação Promocional 2 (Saveiro Cross)
        $raffle2 = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Saveiro Cross Rebaixada',
            'description' => 'Saveiro Cross completa, suspensão a ar legalizada, rodas aro 18, som interno potente, documentação em dia.',
            'price' => 9.90,
            'total_numbers' => 200000,
            'status' => 'active',
            'prize_name' => 'Saveiro Cross 2015 Rebaixada',
            'prize_description' => 'Saveiro na cor branca, bancos de couro, faróis de LED, capota marítima nova.',
            'image_url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80',
            'draw_date' => now()->addDays(45),
        ]);
        $raffle2->seedDefaultPackages();
    }
}
