<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'SuperAdmin',
                'description' => 'Full system access with all permissions',
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access with most permissions',
            ],
            [
                'name' => 'Staff',
                'description' => 'Standard staff access for daily operations',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
