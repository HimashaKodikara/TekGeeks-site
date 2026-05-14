<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateDynamicMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu = [
            [
                'id' => 1,
                'icon' => 'fal fa-lg fa-fw fa-chart-pie',
                'title' => 'Dashboard',
                'page_id' => 1,
                'url' => 'adminpanel/dashboard',
                'parent_id' => 1,
                'is_parent' => 1,
                'show_menu' => 1,
                'parent_order' => 1,
                'child_order' => 1,
                'fOrder' => 1.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'icon' => 'fal fa-lg fa-fw fa-university',
                'title' => 'Monitoring Dashboard',
                'page_id' => 2,
                'url' => 'adminpanel/institute',
                'parent_id' => 2,
                'is_parent' => 1,
                'show_menu' => 1,
                'parent_order' => 2,
                'child_order' => 1,
                'fOrder' => 2.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'icon' => 'fal fa-lg fa-fw fa-school',
                'title' => 'Admin Monitoring Dashboard',
                'page_id' => 3,
                'url' => 'adminpanel/admin-institute',
                'parent_id' => 3,
                'is_parent' => 1,
                'show_menu' => 1,
                'parent_order' => 3,
                'child_order' => 1,
                'fOrder' => 3.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 150,
                'icon' => 'fal fa-lg fa-fw fa-user',
                'title' => 'Administration',
                'page_id' => 150,
                'url' => '#',
                'parent_id' => 0,
                'is_parent' => 1,
                'show_menu' => 1,
                'parent_order' => 7,
                'child_order' => 0,
                'fOrder' => 150.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 151,
                'icon' => '',
                'title' => 'User',
                'page_id' => 151,
                'url' => 'adminpanel/users/users-list',
                'parent_id' => 150,
                'is_parent' => 0,
                'show_menu' => 1,
                'parent_order' => null,
                'child_order' => 1,
                'fOrder' => 150.01,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 152,
                'icon' => '',
                'title' => 'Role',
                'page_id' => 152,
                'url' => 'adminpanel/roles/roles-list',
                'parent_id' => 150,
                'is_parent' => 0,
                'show_menu' => 1,
                'parent_order' => null,
                'child_order' => 2,
                'fOrder' => 150.02,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            [
                'id' => 60,
                'icon' => 'fal fa-lg fa-fw fa-browser',
                'title' => 'Support Module',
                'page_id' => 60,
                'url' => 'adminpanel/common-log',
                'parent_id' => 60,
                'is_parent' => 1,
                'show_menu' => 1,
                'parent_order' => 4,
                'child_order' => 1,
                'fOrder' => 60.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Home Page parent
            [
                'id' => 30,
                'icon' => 'fal fa-lg fa-fw fa-home',
                'title' => 'Home Page',
                'page_id' => 30,
                'url' => '#',
                'parent_id' => 0,
                'is_parent' => 1,
                'show_menu' => 1,
                'parent_order' => 5,
                'child_order' => 0,
                'fOrder' => 30.00,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Home Projects child
            [
                'id' => 31,
                'icon' => '',
                'title' => 'Home Projects',
                'page_id' => 31,
                'url' => 'adminpanel/home-projects-list',
                'parent_id' => 30,
                'is_parent' => 0,
                'show_menu' => 1,
                'parent_order' => null,
                'child_order' => 1,
                'fOrder' => 30.01,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($menu as $item) {
            DB::table('dynamic_menus')->updateOrInsert(
                ['id' => $item['id']],
                [
                    'icon' => $item['icon'],
                    'title' => $item['title'],
                    'page_id' => $item['page_id'],
                    'url' => $item['url'],
                    'parent_id' => $item['parent_id'],
                    'is_parent' => $item['is_parent'],
                    'show_menu' => $item['show_menu'],
                    'parent_order' => $item['parent_order'],
                    'child_order' => $item['child_order'],
                    'fOrder' => $item['fOrder'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
