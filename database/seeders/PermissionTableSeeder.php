<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'dashboard',
            'users-list',
            'roles-list',
            'users-create',
            'users-edit',
            'users-delete',
            'users-status-update',
            'roles-create',
            'roles-edit',
            'roles-delete',
            'common-log',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                [
                    'guard_name' => 'web',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
        }
    }
}
