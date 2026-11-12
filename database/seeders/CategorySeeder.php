<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Alat Elektronik', 'description' => 'Perangkat elektronik untuk kegiatan praktik dan operasional.'],
            ['name' => 'Alat Jaringan', 'description' => 'Perangkat pendukung instalasi dan pengujian jaringan komputer.'],
            ['name' => 'Alat Multimedia', 'description' => 'Peralatan presentasi, dokumentasi, dan produksi media.'],
            ['name' => 'Alat Laboratorium', 'description' => 'Peralatan laboratorium komputer dan elektronika.'],
            ['name' => 'Peralatan Kantor', 'description' => 'Peralatan penunjang administrasi dan kegiatan kantor.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}