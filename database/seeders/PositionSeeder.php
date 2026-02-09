<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Accounting & Tax Section Head',
            'Raw Material Administration',
            'Finish Good Administration',
            'Sales Administration',
            'Production Administration',
            'Initial Material Technical Services',
            'Engineering Administration',
            'Petty Cash Administration',
            'PPIC Administration',
            'Sales Promotion',
            'Product Design Technical Services',
            'Finance Section Head',
            'Corporate Administration Assistant Company Head',
            'GA & Safety Technical Services',
            'HR & GA Dept Head',
            'Comben Technical Services',
            'IT Technical Services',
            'Corporate Manufacturing Asst. Company Head',
            'Brand Activation Dept Head',
            'Management Information System Dept Head',
            'PM Purchase Technical Services',
            'RM Purchase Technical Services',
            'Purchasing Dept Head',
            'Plant Division Head',
            'PPIC Dept Head',
            'PPIC Section Head',
            'Product Development Dept Head',
            'Production Dept Head',
            'Production Sub Dept Head',
            'Production Section Head',
            'PR & KOL Technical Services',
            'Quality Assurance Technical Services',
            'Release Product Technical Services',
            'Validation Technical Services',
            'QC Department Head',
            'QC Section Head',
            'National Sales Dept Head',
            'Sales Supervisor',
            'East Regional Sales Sub Dept Head',
            'West Regional Sales Sub Dept Head (Medan)',
            'Financial Advisor',
            'Finance & Accounting Technical Services',
            'Instrument Technical Services',
            'Engineering Dept Head',
            'People Development Technical Services',
            'Corporate Secretary & Receptionist',
            'Finish Good Unit Head',
            'Packaging Material Administration',
            'Registration Technical Services',
            'QA Dept Head',
            'Quality Assurance Sub Dept Head',
            'Quality Assurance Administration',
            'Microbiology Technical Services',
            'East Java Sales Supervisor',
            'Central Java Sales Supervisor',
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(['name' => $position]);
        }
    }
}
