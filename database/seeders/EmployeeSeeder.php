<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan Role 'Employee' sudah ada
        $roleEmployee = Role::firstOrCreate(['name' => 'Employee']);

        // 2. Definisi Data Pegawai (NAMA DEPARTMENT DISESUAIKAN DENGAN DB)
        $dataPegawai = [
            // 1. MIS -> Ubah jadi "Management Information System" (sesuai screenshot)
            [
                'name' => 'MIS Staff',
                'email' => 'cc.initiator@samcofarma.co.id',
                'phone' => '082122556565',
                'position' => 'IT Technical Services',
                'department' => 'Management Information System', // Disesuaikan
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'MIS Manager',
                'email' => 'cc.mgrmis@samcofarma.co.id',
                'phone' => '081298765432',
                'position' => 'Management Information System Dept Head',
                'department' => 'Management Information System', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan']
            ],

            // 2. Prodev -> Ubah jadi "Product Development"
            [
                'name' => 'Staff Prodev',
                'email' => 'cc.prodev@samcofarma.co.id',
                'phone' => '0822556556565',
                'position' => 'People Development Tech Service',
                'department' => 'Product Development', // Disesuaikan
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'Prodev Head',
                'email' => 'cc.prodevhead@samcofarma.co.id',
                'phone' => '081255685858',
                'position' => 'Product Development Dept Head',
                'department' => 'Product Development', // Disesuaikan
                'permissions' => ['Review Prodev Manager', 'Approve Manager']
            ],

            // 3. PPIC -> Cek DB, apakah "PPIC" atau "PPIC & Warehouse"?
            // Jika di DB pakai "PPIC & Warehouse", gunakan itu. Jika "PPIC", biarkan.
            // Asumsi berdasarkan screenshot baris ke-2 adalah "PPIC & Warehouse"
            [
                'name' => 'PPIC Staff',
                'email' => 'cc.ppic@samcofarma.co.id',
                'phone' => '081255648556',
                'position' => 'PPIC Administrasion',
                'department' => 'PPIC & Warehouse', // Disesuaikan (atau 'PPIC' jika ingin yang singkat)
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'PPIC Head',
                'email' => 'cc.mgrppic@samcofarma.co.id',
                'phone' => '081200000001',
                'position' => 'PPIC Dept Head',
                'department' => 'PPIC & Warehouse', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan', 'Review PPIC Manager']
            ],

            // 4. QC -> Ubah jadi "Quality Control"
            [
                'name' => 'QC Staff',
                'email' => 'cc.qc@samcofarma.co.id',
                'phone' => '08122545665',
                'position' => 'QC Section Head',
                'department' => 'Quality Control', // Disesuaikan
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'QC Manager',
                'email' => 'cc.mgrqc@samcofarma.co.id',
                'phone' => '08125452122',
                'position' => 'QC Departement Head',
                'department' => 'Quality Control', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan']
            ],

            // 5. QA -> Ubah jadi "Quality Assurance"
            [
                'name' => 'Staff QA',
                'email' => 'cc.qa@samcofarma.co.id',
                'phone' => '08122545664',
                'position' => 'Quality Assurance Administration',
                'department' => 'Quality Assurance', // Disesuaikan
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'QA SPV',
                'email' => 'cc.spv@samcofarma.co.id',
                'phone' => '08126556987',
                'position' => 'QA Section Head',
                'department' => 'Quality Assurance', // Disesuaikan
                'permissions' => ['Approve QA SPV']
            ],
            [
                'name' => 'QA Manager',
                'email' => 'cc.mgrqa@samcofarma.co.id',
                'phone' => '0812545565',
                'position' => 'QA Dept Head',
                'department' => 'Quality Assurance', // Disesuaikan
                'permissions' => ['Approve QA Manager']
            ],

            // 6. Plant -> (Di Screenshot sepertinya belum ada "Plant" formal,
            // tapi ada "Production" atau "Engineering". Jika "Plant" memang nama resminya, biarkan)
            [
                'name' => 'Plant Head',
                'email' => 'cc.plant@samcofarma.co.id',
                'phone' => '08125545685',
                'position' => 'Plant Divsion Head',
                'department' => 'Plant', // Pastikan nama ini sesuai keinginan
                'permissions' => ['Approve Plant Manager']
            ],

            // 7. HRD -> Ubah jadi "HR & GA" (Sesuai screenshot baris 8)
            [
                'name' => 'Staff HRD',
                'email' => 'cc.hrd@samcofarma.co.id',
                'phone' => '08125546598',
                'position' => 'GA & SAFETY Technical Services',
                'department' => 'HR & GA', // Disesuaikan
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'Manager HRD',
                'email' => 'cc.mgrhrd@samcofarma.co.id',
                'phone' => '0812556555',
                'position' => 'HR & GA Dept Head',
                'department' => 'HR & GA', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan']
            ],
            [
                'name' => 'Staff Marketing',
                'email' => 'cc.marketing@samcofarma.co.id',
                'phone' => '08125546598',
                'position' => 'GA & SAFETY Technical Services',
                'department' => 'Marketing', // Disesuaikan (Marketing ada di screenshot)
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'Manager Marketing',
                'email' => 'cc.marketingmgr@samcofarma.co.id',
                'phone' => '0812554536555',
                'position' => 'Junior Marketing Dept Head',
                'department' => 'Marketing', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan']
            ],
            [
                'name' => 'Staff Engineering',
                'email' => 'cc.engineering@samcofarma.co.id',
                'phone' => '081255464598',
                'position' => 'Staff Engineering',
                'department' => 'Engineering', // Disesuaikan (Marketing ada di screenshot)
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'Manager Engineering',
                'email' => 'cc.engineeringmgr@samcofarma.co.id',
                'phone' => '0812554536635',
                'position' => 'Engineering Dept Head',
                'department' => 'Engineering', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan']
            ],
            [
                'name' => 'Staff Sales',
                'email' => 'cc.sales@samcofarma.co.id',
                'phone' => '081255464591',
                'position' => 'Sales Administration',
                'department' => 'Sales', // Disesuaikan (Marketing ada di screenshot)
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'Manager Sales',
                'email' => 'cc.salesmgr@samcofarma.co.id',
                'phone' => '0812554536635',
                'position' => 'Sales Dept Head',
                'department' => 'Sales', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan']
            ],
            [
                'name' => 'Staff Finance',
                'email' => 'cc.finance@samcofarma.co.id',
                'phone' => '081255459431',
                'position' => 'Finance & Accounting Technical Services',
                'department' => 'Finance & Accounting', // Disesuaikan (Marketing ada di screenshot)
                'permissions' => ['Create Change Control']
            ],
            [
                'name' => 'Manager Finance',
                'email' => 'cc.financemgr@samcofarma.co.id',
                'phone' => '0812874536635',
                'position' => 'Finance Dept Head',
                'department' => 'Finance & Accounting', // Disesuaikan
                'permissions' => ['Approve Manager', 'PIC Action Plan']
            ],
        ];

        DB::transaction(function () use ($dataPegawai) {
            foreach ($dataPegawai as $pegawai) {

                // PENTING: Gunakan trim() untuk membuang spasi tidak sengaja
                // firstOrCreate akan mencari nama department yang SAMA PERSIS.
                $dept = Department::firstOrCreate(
                    ['name' => trim($pegawai['department'])]
                );

                $pos = Position::firstOrCreate(
                    ['name' => $pegawai['position']],
                    ['department_id' => $dept->id]
                );

                $user = User::firstOrCreate(
                    ['email' => $pegawai['email']],
                    [
                        'name' => $pegawai['name'],
                        'password' => Hash::make('12345678'),
                        'email_verified_at' => now(),
                    ]
                );

                $user->assignRole('Employee');

                if (!empty($pegawai['permissions'])) {
                    foreach ($pegawai['permissions'] as $permName) {
                        $permission = Permission::where('name', $permName)->first();
                        if ($permission) {
                            $user->givePermissionTo($permission);
                        }
                    }
                }

                $empCode = 'EMP-' . strtoupper(Str::random(8));

                Employee::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_id' => $dept->id,
                        'position_id' => $pos->id,
                        'employee_code' => $empCode,
                        'name' => $pegawai['name'],
                        'address' => 'Alamat Kantor Samco Farma',
                        'gender' => 'male',
                        'phone' => $pegawai['phone'],
                        'updated_at' => now(),
                    ]
                );

                $this->command->info("Connected: " . $pegawai['name'] . " -> Department: " . $dept->name);
            }
        });
    }
}
