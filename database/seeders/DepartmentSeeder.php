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
            ['name' => 'Finance & Accounting', 'short_name' => 'FA'],
            ['name' => 'PPIC & Warehouse', 'short_name' => 'PI'],
            ['name' => 'Sales', 'short_name' => 'SL'],
            ['name' => 'Production', 'short_name' => 'PD'],
            ['name' => 'Quality Control', 'short_name' => 'QC'],
            ['name' => 'Engineering', 'short_name' => 'TK'],
            ['name' => 'Marketing', 'short_name' => 'MK'],
            ['name' => 'HR & GA', 'short_name' => 'HR'],
            ['name' => 'Management Information System', 'short_name' => 'MS'],
            ['name' => 'Purchasing', 'short_name' => 'PC'],
            ['name' => 'Product Development', 'short_name' => 'RD'],
            ['name' => 'Quality Assurance', 'short_name' => 'QA'],
            ['name' => 'Plant', 'short_name' => 'PLT'],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['name' => $department['name']],
                ['short_name' => $department['short_name']]
            );
        }
    }
}
