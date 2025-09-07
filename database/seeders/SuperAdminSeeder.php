<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        Utilisateur::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'nom' => 'Super Admin',
                'password' => Hash::make('motdepassefort'),
                'role' => 'super_admin',
            ]
        );
    }
}
