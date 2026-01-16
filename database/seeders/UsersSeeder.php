<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            ['name' => 'Owner', 'password' => Hash::make('password')]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('admin')]
        );

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@example.com'],
            ['name' => 'Cashier', 'password' => Hash::make('password')]
        );

        $ownerRole = Role::firstWhere('name', 'owner');
        $superAdminRole = Role::firstWhere('name', 'super_admin');
        $cashierRole = Role::firstWhere('name', 'cashier');

        if ($ownerRole) {
            $owner->assignRole($ownerRole);
        }

        if ($superAdminRole) {
            $admin->assignRole($superAdminRole);
        }

        if ($cashierRole) {
            $cashier->assignRole($cashierRole);
        }
    }
}
