import bcrypt from 'bcryptjs';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  const users = [
    { name: 'Administrator Sistem', username: 'admin', email: 'admin@sistema.test', role: 'admin', phone: '081200000001', address: 'Ruang Administrasi' },
    { name: 'Petugas Inventaris', username: 'petugas', email: 'petugas@sistema.test', role: 'petugas', phone: '081200000002', address: 'Gudang Inventaris' },
    { name: 'Rina Peminjam', username: 'peminjam', email: 'peminjam@sistema.test', role: 'peminjam', phone: '081200000003', address: 'Jl. Pendidikan No. 10' },
    { name: 'Budi Santoso', username: 'budi', email: 'budi@sistema.test', role: 'peminjam', phone: '081200000004', address: 'Jl. Melati No. 22' },
  ];

  for (const u of users) {
    await prisma.user.upsert({
      where: { username: u.username },
      update: { name: u.name, email: u.email, role: u.role, phone: u.phone, address: u.address },
      create: { ...u, password: await bcrypt.hash(u.role === 'admin' ? 'admin123' : u.role === 'petugas' ? 'petugas123' : 'peminjam123', 10) },
    });
  }

  const categories: [string, string][] = [
    ['Alat Elektronik', 'Perangkat elektronik untuk kegiatan praktik dan operasional.'],
    ['Alat Jaringan', 'Perangkat pendukung instalasi dan pengujian jaringan komputer.'],
    ['Alat Multimedia', 'Peralatan presentasi, dokumentasi, dan produksi media.'],
    ['Alat Laboratorium', 'Peralatan laboratorium komputer dan elektronika.'],
    ['Peralatan Kantor', 'Peralatan penunjang administrasi dan kegiatan kantor.'],
  ];

  for (const [name, description] of categories) {
    await prisma.category.upsert({ where: { name }, update: { description }, create: { name, description } });
  }

  const cat = Object.fromEntries((await prisma.category.findMany()).map((c) => [c.name, c.id]));

  const tools: [string, string, string, number, string, string, string, string][] = [
    ['MLT-001', 'Proyektor Epson EB-X06', 'Alat Multimedia', 4, 'baik', 'Gudang Lt. 1 - Rak A1', 'tersedia', 'Proyektor portabel 3600 lumens untuk presentasi.'],
    ['ELK-001', 'Laptop Lenovo ThinkPad E14', 'Alat Elektronik', 6, 'baik', 'Gudang Lt. 1 - Rak B2', 'tersedia', 'Laptop untuk kegiatan praktik dan pelatihan.'],
    ['MLT-002', 'Kamera Canon EOS 250D', 'Alat Multimedia', 2, 'baik', 'Gudang Lt. 1 - Rak A2', 'tersedia', 'Kamera DSLR untuk dokumentasi kegiatan.'],
    ['JRG-001', 'Router MikroTik RB951G', 'Alat Jaringan', 5, 'baik', 'Gudang Lt. 2 - Rak C1', 'tersedia', 'Router wireless untuk praktik konfigurasi jaringan.'],
    ['JRG-002', 'Switch Cisco Catalyst 24 Port', 'Alat Jaringan', 3, 'baik', 'Gudang Lt. 2 - Rak C1', 'tersedia', 'Switch manageable 24 port.'],
    ['JRG-003', 'Access Point TP-Link EAP225', 'Alat Jaringan', 8, 'baik', 'Gudang Lt. 2 - Rak C2', 'tersedia', 'Access point ceiling mount.'],
    ['JRG-004', 'Kabel UTP Cat6 Roll 305m', 'Alat Jaringan', 6, 'baik', 'Gudang Lt. 2 - Rak C3', 'tersedia', 'Kabel UTP Cat6 untuk instalasi jaringan.'],
    ['JRG-005', 'Tang Crimping RJ45', 'Alat Jaringan', 12, 'baik', 'Gudang Lt. 2 - Rak C3', 'tersedia', 'Tang crimping untuk konektor RJ45.'],
    ['LAB-001', 'Multimeter Digital Sanwa', 'Alat Laboratorium', 6, 'baik', 'Lab Elektronika - Lemari 1', 'tersedia', 'Multimeter digital untuk pengukuran.'],
    ['LAB-002', 'Solder Station Digital', 'Alat Laboratorium', 4, 'rusak_ringan', 'Lab Elektronika - Lemari 2', 'perbaikan', 'Solder station dengan pengatur suhu.'],
    ['MLT-003', 'Sound System Portable', 'Alat Multimedia', 3, 'baik', 'Gudang Lt. 1 - Rak A3', 'tersedia', 'Speaker aktif portabel dengan mikrofon.'],
    ['MLT-004', 'Tripod Kamera', 'Alat Multimedia', 5, 'baik', 'Gudang Lt. 1 - Rak A2', 'tersedia', 'Tripod aluminium untuk kamera.'],
    ['KNT-001', 'Printer Canon iP2770', 'Peralatan Kantor', 4, 'baik', 'Ruang Administrasi', 'tersedia', 'Printer inkjet untuk cetak dokumen.'],
    ['KNT-002', 'Scanner Dokumen Canon', 'Peralatan Kantor', 2, 'rusak_berat', 'Ruang Administrasi', 'tidak_tersedia', 'Scanner flatbed untuk digitalisasi.'],
  ];

  for (const [code, name, category, stock, condition, location, status, description] of tools) {
    await prisma.tool.upsert({
      where: { code },
      update: { name, categoryId: cat[category], stockTotal: stock, condition, location, status, description },
      create: { code, name, categoryId: cat[category], stockTotal: stock, stockAvailable: stock, description, condition, location, status },
    });
  }

  const settings: [string, string, string, string, string][] = [
    ['app_name', 'SISTEMA', 'string', 'general', 'Nama Aplikasi'],
    ['app_tagline', 'Sistem Peminjaman Alat', 'string', 'general', 'Tagline'],
    ['app_description', 'Platform digital untuk pengelolaan inventaris, peminjaman, dan pengembalian alat.', 'string', 'general', 'Deskripsi'],
    ['contact_email', 'admin@sistema.test', 'string', 'contact', 'Email Kontak'],
    ['contact_phone', '(021) 1234-5678', 'string', 'contact', 'Telepon'],
    ['contact_address', 'Jl. Pendidikan No. 10, Jakarta', 'string', 'contact', 'Alamat'],
    ['fine_enabled', '1', 'boolean', 'loan', 'Aktifkan Denda'],
    ['fine_per_day', '5000', 'integer', 'loan', 'Denda per Hari'],
    ['max_loan_days', '14', 'integer', 'loan', 'Maksimal Lama Peminjaman'],
    ['announcement_title', 'Pengambilan Alat Wajib Menunjukkan Identitas', 'string', 'announcement', 'Judul Pengumuman'],
    ['announcement_content', 'Peminjam wajib membawa kartu identitas saat mengambil alat. Pengembalian dilakukan paling lambat sesuai tanggal pada formulir.', 'string', 'announcement', 'Isi Pengumuman'],
    ['announcement_active', '1', 'boolean', 'announcement', 'Tampilkan Pengumuman'],
  ];

  for (const [key, value, type, group, label] of settings) {
    await prisma.setting.upsert({ where: { key }, update: { value, type, group, label }, create: { key, value, type, group, label } });
  }

  console.log('Seed selesai.');
}

main().finally(() => prisma.$disconnect());
