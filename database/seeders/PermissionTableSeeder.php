<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard',              'dynamic_menu_id' => 1],

            // Users (child of Administration id=150, page_id=151)
            ['name' => 'users-list',             'dynamic_menu_id' => 151],
            ['name' => 'users-create',           'dynamic_menu_id' => 151],
            ['name' => 'users-edit',             'dynamic_menu_id' => 151],
            ['name' => 'users-delete',           'dynamic_menu_id' => 151],
            ['name' => 'users-status-update',    'dynamic_menu_id' => 151],

            // Roles (child of Administration id=150, page_id=152)
            ['name' => 'roles-list',             'dynamic_menu_id' => 152],
            ['name' => 'roles-create',           'dynamic_menu_id' => 152],
            ['name' => 'roles-edit',             'dynamic_menu_id' => 152],
            ['name' => 'roles-delete',           'dynamic_menu_id' => 152],

            // Support Module
            ['name' => 'common-log',             'dynamic_menu_id' => 60],

            // Home Projects (child id=31 under Home Page parent id=30)
            ['name' => 'home-project-list',      'dynamic_menu_id' => 31],
            ['name' => 'home-project-create',    'dynamic_menu_id' => 31],
            ['name' => 'home-project-edit',      'dynamic_menu_id' => 31],
            ['name' => 'home-project-delete',    'dynamic_menu_id' => 31],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'guard_name' => 'web',
                    'dynamic_menu_id' => $permission['dynamic_menu_id'],
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
