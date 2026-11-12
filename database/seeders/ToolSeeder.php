<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tool;
use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        $tools = [
            [
                'code' => 'MLT-001',
                'name' => 'Proyektor Epson EB-X06',
                'category' => 'Alat Multimedia',
                'description' => 'Proyektor portabel 3600 lumens untuk kegiatan presentasi dan pembelajaran.',
                'stock_total' => 4,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 1 - Rak A1',
                'status' => 'tersedia',
            ],
            [
                'code' => 'ELK-001',
                'name' => 'Laptop Lenovo ThinkPad E14',
                'category' => 'Alat Elektronik',
                'description' => 'Laptop untuk kegiatan praktik, pelatihan, dan pekerjaan lapangan.',
                'stock_total' => 6,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 1 - Rak B2',
                'status' => 'tersedia',
            ],
            [
                'code' => 'MLT-002',
                'name' => 'Kamera Canon EOS 250D',
                'category' => 'Alat Multimedia',
                'description' => 'Kamera DSLR beserta lensa kit untuk dokumentasi kegiatan.',
                'stock_total' => 2,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 1 - Rak A2',
                'status' => 'tersedia',
            ],
            [
                'code' => 'JRG-001',
                'name' => 'Router MikroTik RB951G',
                'category' => 'Alat Jaringan',
                'description' => 'Router wireless untuk praktik konfigurasi jaringan.',
                'stock_total' => 5,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 2 - Rak C1',
                'status' => 'tersedia',
            ],
            [
                'code' => 'JRG-002',
                'name' => 'Switch Cisco Catalyst 24 Port',
                'category' => 'Alat Jaringan',
                'description' => 'Switch manageable 24 port untuk praktik instalasi jaringan.',
                'stock_total' => 3,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 2 - Rak C1',
                'status' => 'tersedia',
            ],
            [
                'code' => 'JRG-003',
                'name' => 'Access Point TP-Link EAP225',
                'category' => 'Alat Jaringan',
                'description' => 'Access point ceiling mount untuk pengujian jaringan nirkabel.',
                'stock_total' => 8,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 2 - Rak C2',
                'status' => 'tersedia',
            ],
            [
                'code' => 'JRG-004',
                'name' => 'Kabel UTP Cat6 Roll 305m',
                'category' => 'Alat Jaringan',
                'description' => 'Kabel UTP Cat6 untuk instalasi dan praktik pengkabelan jaringan.',
                'stock_total' => 6,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 2 - Rak C3',
                'status' => 'tersedia',
            ],
            [
                'code' => 'JRG-005',
                'name' => 'Tang Crimping RJ45',
                'category' => 'Alat Jaringan',
                'description' => 'Tang crimping untuk pemasangan konektor RJ45.',
                'stock_total' => 12,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 2 - Rak C3',
                'status' => 'tersedia',
            ],
            [
                'code' => 'LAB-001',
                'name' => 'Multimeter Digital Sanwa',
                'category' => 'Alat Laboratorium',
                'description' => 'Multimeter digital untuk pengukuran tegangan, arus, dan hambatan.',
                'stock_total' => 6,
                'condition' => 'baik',
                'location' => 'Lab Elektronika - Lemari 1',
                'status' => 'tersedia',
            ],
            [
                'code' => 'LAB-002',
                'name' => 'Solder Station Digital',
                'category' => 'Alat Laboratorium',
                'description' => 'Solder station dengan pengatur suhu untuk praktik elektronika.',
                'stock_total' => 4,
                'condition' => 'rusak_ringan',
                'location' => 'Lab Elektronika - Lemari 2',
                'status' => 'perbaikan',
            ],
            [
                'code' => 'MLT-003',
                'name' => 'Sound System Portable',
                'category' => 'Alat Multimedia',
                'description' => 'Speaker aktif portabel dengan mikrofon untuk kegiatan indoor.',
                'stock_total' => 3,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 1 - Rak A3',
                'status' => 'tersedia',
            ],
            [
                'code' => 'MLT-004',
                'name' => 'Tripod Kamera',
                'category' => 'Alat Multimedia',
                'description' => 'Tripod aluminium untuk kamera dan perekaman video.',
                'stock_total' => 5,
                'condition' => 'baik',
                'location' => 'Gudang Lt. 1 - Rak A2',
                'status' => 'tersedia',
            ],
            [
                'code' => 'KNT-001',
                'name' => 'Printer Canon iP2770',
                'category' => 'Peralatan Kantor',
                'description' => 'Printer inkjet untuk kebutuhan cetak dokumen.',
                'stock_total' => 4,
                'condition' => 'baik',
                'location' => 'Ruang Administrasi',
                'status' => 'tersedia',
            ],
            [
                'code' => 'KNT-002',
                'name' => 'Scanner Dokumen Canon',
                'category' => 'Peralatan Kantor',
                'description' => 'Scanner flatbed untuk digitalisasi dokumen.',
                'stock_total' => 2,
                'condition' => 'rusak_berat',
                'location' => 'Ruang Administrasi',
                'status' => 'tidak_tersedia',
            ],
        ];

        foreach ($tools as $tool) {
            Tool::updateOrCreate(
                ['code' => $tool['code']],
                [
                    'name' => $tool['name'],
                    'category_id' => $categories[$tool['category']],
                    'description' => $tool['description'],
                    'stock_total' => $tool['stock_total'],
                    'stock_available' => $tool['stock_total'],
                    'condition' => $tool['condition'],
                    'location' => $tool['location'],
                    'status' => $tool['status'],
                ]
            );
        }
    }
}