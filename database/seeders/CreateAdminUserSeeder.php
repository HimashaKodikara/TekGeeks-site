<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SYSTEM ADMIN USER
        |--------------------------------------------------------------------------
        */

        $adminUser = User::updateOrCreate(
            ['email' => 'superadmin@tekgeeks.net'],
            [
                'name'     => 'Super Admin',
                'nic'      => '111111111V',
                'password' => bcrypt('Tek@Admin12'),
            ]
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'System Admin', 'guard_name' => 'web']
        );

        $allPermissions = Permission::pluck('id')->toArray();
        $adminRole->syncPermissions($allPermissions);

        if (!$adminUser->hasRole($adminRole->name)) {
            $adminUser->assignRole($adminRole);
        }

    }
}
