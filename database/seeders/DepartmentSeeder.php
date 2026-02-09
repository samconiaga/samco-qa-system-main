<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Finance & Accounting',
            'PPIC & Warehouse',
            'Sales',
            'Production',
            'Quality Control',
            'Engineering',
            'Marketing',
            'HR & GA',
            'Management Information System',
            'Purchasing',
            'Product Development',
            'Quality Assurance',
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department]);
        }
    }
}
