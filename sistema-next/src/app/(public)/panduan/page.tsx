import Link from "next/link";
import { Check } from "lucide-react";

const STEPS = [
  ["Login ke sistem", "Masuk menggunakan akun peminjam yang sudah terdaftar."],
  ["Pilih alat yang tersedia", "Buka Daftar Alat, gunakan pencarian atau filter kategori."],
  ["Isi formulir peminjaman", "Tentukan jumlah, tanggal pinjam, tanggal kembali, dan keperluan."],
  ["Tunggu persetujuan petugas", "Pantau status melalui menu Peminjaman Saya."],
  ["Ambil alat", "Ambil alat di gudang dengan menunjukkan identitas dan kode peminjaman."],
  ["Kembalikan sesuai jadwal", "Ajukan pengembalian melalui sistem dan serahkan alat ke petugas."],
];

const RULES = [
  "Peminjam wajib memiliki akun yang aktif.",
  "Jumlah peminjaman tidak boleh melebihi stok tersedia.",
  "Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.",
  "Alat harus dikembalikan sesuai kondisi saat dipinjam.",
  "Keterlambatan dapat dikenakan denda sesuai kebijakan.",
];

export default function PanduanPage() {
  return (
    <section className="bg-canvas">
      <div className="border-b border-line bg-surface">
        <div className="mx-auto max-w-6xl px-4 py-10 sm:px-6">
          <p className="section-eyebrow">Panduan</p>
          <h1 className="mt-2 font-display text-2xl font-semibold text-heading sm:text-3xl">Panduan Peminjaman Alat</h1>
        </div>
      </div>

      <div className="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.6fr_1fr]">
        <div className="card card-pad">
          <ol className="space-y-8">
            {STEPS.map(([title, desc], i) => (
              <li key={i} className="flex gap-4">
                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-navy text-sm font-semibold text-white">{i + 1}</span>
                <div>
                  <p className="font-display text-base font-semibold text-heading">{title}</p>
                  <p className="mt-1 text-sm leading-relaxed text-ink-secondary">{desc}</p>
                </div>
              </li>
            ))}
          </ol>
        </div>

        <div className="space-y-6">
          <div className="card card-pad">
            <h2 className="font-display text-base font-semibold text-heading">Ketentuan Peminjaman</h2>
            <ul className="mt-4 space-y-3 text-sm text-ink-secondary">
              {RULES.map((rule) => (
                <li key={rule} className="flex gap-2.5">
                  <Check className="mt-0.5 h-4 w-4 shrink-0 text-success" />
                  <span>{rule}</span>
                </li>
              ))}
            </ul>
          </div>

          <div className="card card-pad bg-navy text-white">
            <h2 className="font-display text-base font-semibold text-white">Siap mengajukan peminjaman?</h2>
            <p className="mt-2 text-sm text-white/70">Masuk ke sistem untuk mulai mengajukan peminjaman alat.</p>
            <div className="mt-4 flex flex-col gap-2">
              <Link href="/login" className="btn bg-white text-navy hover:bg-white/90">Masuk ke Sistem</Link>
              <Link href="/alat" className="btn border border-white/25 text-white hover:bg-white/10">Lihat Daftar Alat</Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}