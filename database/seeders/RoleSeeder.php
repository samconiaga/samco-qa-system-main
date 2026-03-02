<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(['name' => 'Administrator']);
        Role::updateOrCreate(['name' => 'Employee']);
        Permission::updateOrCreate(['name' => 'Create Master Data']);
        Permission::updateOrCreate(['name' => 'Create Change Control']);
        Permission::updateOrCreate(['name' => 'Approve Manager']);
        Permission::updateOrCreate(['name' => 'Approve QA SPV']);
        Permission::updateOrCreate(['name' => 'Review Prodev Manager']);
        Permission::updateOrCreate(['name' => 'Review PPIC Manager']);
        Permission::updateOrCreate(['name' => 'Approve QA Manager']);
        Permission::updateOrCreate(['name' => 'Approve Plant Manager']);
        Permission::updateOrCreate(['name' => 'PIC Action Plan']);
        Permission::updateOrCreate(['name' => 'Create CAPA']);
    }
}
