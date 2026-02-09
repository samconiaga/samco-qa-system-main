<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => 'password', // Default password
        ]);
        
        $users->assignRole('Administrator')->givePermissionTo([
            'Create Master Data',
        ]);
    }
}
