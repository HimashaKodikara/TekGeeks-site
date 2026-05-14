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
            'institute-dashboard',
            'admin-institute-dashboard',
            'users-list',
            'roles-list',
            'users-create',
            'users-edit',
            'users-delete',
            'users-status-update',
            'roles-create',
            'roles-edit',
            'roles-delete',
            'faq-list',
            'faq-create',
            'faq-edit',
            'faq-status-update',
            'nic-duplicate-records-list',
            'nic-duplicate-records-view',
            'common-log',
        ];

        $dynamicID = [
            '1',
            '2',
            '3',
            '151',
            '152',
            '151',
            '151',
            '151',
            '151',
            '152',
            '152',
            '152',
            '11',
            '11',
            '11',
            '11',
            '21',
            '21',

            '60',
        ];

        for ($i = 0; $i < count($permissions); $i++) {
            Permission::updateOrCreate(
                ['name' => $permissions[$i]], // Match condition
                [
                    'dynamic_menu_id' => $dynamicID[$i],
                    'guard_name' => 'web',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            );
        }
    }
}
