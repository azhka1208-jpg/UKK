import Link from "next/link";
import { ClipboardList, Megaphone, PackageCheck, PackageSearch, ShieldCheck, Tags, Wrench } from "lucide-react";
import StatCard from "@/components/stat-card";
import ToolCard from "@/components/tool-card";
import { prisma } from "@/lib/prisma";
import { settingValue } from "@/lib/utils";

const STEPS = [
  ["Login ke sistem", "Gunakan akun peminjam yang terdaftar."],
  ["Pilih alat tersedia", "Periksa stok dan kondisi alat."],
  ["Isi formulir peminjaman", "Lengkapi tanggal dan keperluan."],
  ["Tunggu persetujuan petugas", "Pengajuan diperiksa petugas."],
  ["Ambil alat", "Ambil alat sesuai jadwal disetujui."],
  ["Kembalikan sesuai jadwal", "Kembalikan sebelum batas waktu."],
];

export default async function HomePage() {
  const [totalTools, totalCategories, availableTools, totalBorrowings, tools] = await Promise.all([
    prisma.tool.count(),
    prisma.category.count(),
    prisma.tool.count({ where: { status: "tersedia", stockAvailable: { gt: 0 } } }),
    prisma.borrowing.count(),
    prisma.tool.findMany({
      include: { category: true },
      orderBy: [{ status: "asc" }, { stockAvailable: "desc" }],
      take: 6,
    }),
  ]);

  const announcementActive = (await settingValue("announcement_active", "0")) === "1";
  const announcementTitle = await settingValue("announcement_title");
  const announcementContent = await settingValue("announcement_content");

  return (
    <>
      <section className="border-b border-line bg-surface">
        <div className="mx-auto grid max-w-6xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:py-20">
          <div>
            <span className="inline-flex items-center gap-2 rounded-full border border-line bg-canvas px-3 py-1 text-xs font-medium text-heading">
              <ShieldCheck className="h-3.5 w-3.5" />
              Sistem Inventaris Terpadu
            </span>
            <h1 className="mt-5 font-display text-3xl font-bold leading-tight text-heading sm:text-4xl lg:text-[2.6rem]">
              Kelola Peminjaman Alat dengan Lebih Mudah dan Terorganisir.
            </h1>
            <p className="mt-4 max-w-xl text-base leading-relaxed text-ink-secondary">
              Platform digital untuk membantu pengelolaan inventaris, peminjaman, dan pengembalian alat secara terstruktur, transparan, dan efisien.
            </p>
            <div className="mt-7 flex flex-wrap items-center gap-3">
              <Link href="/alat" className="btn-primary">
                <PackageSearch className="h-4 w-4" />
                Lihat Daftar Alat
              </Link>
              <Link href="/login" className="btn-secondary">
                Masuk ke Sistem
              </Link>
            </div>

            <div className="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-ink-secondary">
              <span className="flex items-center gap-2">
                <PackageCheck className="h-4 w-4 text-success" /> Data stok aktual
              </span>
              <span className="flex items-center gap-2">
                <PackageCheck className="h-4 w-4 text-success" /> Persetujuan terverifikasi
              </span>
            </div>
          </div>

          <div className="relative">
            <div className="flex aspect-[4/3] items-center justify-center rounded-2xl border border-line bg-accent">
              <PackageSearch className="h-24 w-24 text-navy/40" />
            </div>
          </div>
        </div>
      </section>

      <section className="bg-canvas">
        <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6">
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatCard label="Total Alat" value={totalTools} icon={<Wrench className="h-5 w-5" />} tone="navy" hint="Jenis alat terdata" />
            <StatCard label="Kategori Alat" value={totalCategories} icon={<Tags className="h-5 w-5" />} tone="slate" hint="Kelompok inventaris" />
            <StatCard label="Alat Tersedia" value={availableTools} icon={<PackageCheck className="h-5 w-5" />} tone="success" hint="Siap dipinjam" />
            <StatCard label="Total Peminjaman" value={totalBorrowings} icon={<ClipboardList className="h-5 w-5" />} tone="warning" hint="Seluruh transaksi" />
          </div>
        </div>
      </section>

      <section className="bg-surface">
        <div className="mx-auto max-w-6xl px-4 py-14 sm:px-6">
          <div className="flex items-end justify-between">
            <div>
              <p className="section-eyebrow">Katalog Inventaris</p>
              <h2 className="mt-2 font-display text-2xl font-semibold text-heading">Alat yang tersedia</h2>
            </div>
            <Link href="/alat" className="btn-secondary btn-sm">
              Lihat semua
            </Link>
          </div>

          <div className="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            {tools.map((tool) => (
              <ToolCard key={tool.id} tool={tool} href={`/alat/${tool.id}`} />
            ))}
          </div>
        </div>
      </section>

      <section className="border-y border-line bg-canvas">
        <div className="mx-auto max-w-6xl px-4 py-14 sm:px-6">
          <p className="section-eyebrow">Panduan Peminjaman</p>
          <h2 className="mt-2 font-display text-2xl font-semibold text-heading">Alur peminjaman yang jelas</h2>
          <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {STEPS.map(([title, desc], index) => (
              <div key={index} className="card card-pad">
                <div className="flex items-center justify-between">
                  <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-sm font-semibold text-heading">{index + 1}</span>
                </div>
                <p className="mt-3 font-display text-sm font-semibold text-heading">{title}</p>
                <p className="mt-1 text-sm text-ink-secondary">{desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {announcementActive && announcementTitle ? (
        <section className="bg-surface">
          <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6">
            <div className="rounded-xl border border-navy/15 bg-accent p-6 sm:p-8">
              <div className="flex flex-col gap-4 sm:flex-row">
                <span className="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-navy text-white">
                  <Megaphone className="h-5 w-5" />
                </span>
                <div>
                  <p className="text-xs font-semibold uppercase tracking-wide text-heading/70">Pengumuman</p>
                  <h3 className="mt-1 font-display text-lg font-semibold text-heading">{announcementTitle}</h3>
                  <p className="mt-2 max-w-3xl text-sm leading-relaxed text-ink-secondary">{announcementContent}</p>
                </div>
              </div>
            </div>
          </div>
        </section>
      ) : null}
    </>
  );
}