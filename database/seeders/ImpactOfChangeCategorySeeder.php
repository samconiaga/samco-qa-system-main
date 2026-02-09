<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ImpactOfChangeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ImpactOfChangeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Kolom 1
            'DPI-PPI PP',
            'DPI-PPI KP',
            'DPI-PPI KS',
            'Formula',
            'Spesifikasi',
            'Stabilitas',
            'Transfer Metode',
            'Daftar Pemasok',
            'Kualifikasi Vendor',
            'Final Artwork',
            'Uji BE',
            'Uji Disolusi Terbanding',

            // Kolom 2
            'Registrasi Baru',
            'Registrasi Variasi',
            'URS',
            'KD',
            'KT',
            'KO',
            'KK',
            'Validasi Proses',
            'Validasi Metode Analisa',
            'Validasi Pembersihan',
            'Validasi Aseptis',
            'Validasi Sistem Komputer',

            // Kolom 3
            'Training',
            'Struktur Organisasi',
            'Job desk',
            'Manual Mutu',
            'PM',
            'PK',
            'FO',
            'DA',
            'GA',
            'Jadwal Maintenance',
            'SMF',
            'Perizinan',

            // Kolom 4
            'IBPR',
            'IADL',
            'RBT',
            'SPP disetujui',
        ];

        foreach ($categories as $category) {
            ImpactOfChangeCategory::updateOrCreate([
                'name' => $category,
            ]);
        }
    }
}
