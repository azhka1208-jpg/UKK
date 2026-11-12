import { prisma } from "@/lib/prisma";

export default async function TentangPage() {
  const [tools, categories, available, borrowings] = await Promise.all([
    prisma.tool.count(),
    prisma.category.count(),
    prisma.tool.count({ where: { status: "tersedia", stockAvailable: { gt: 0 } } }),
    prisma.borrowing.count(),
  ]);

  return (
    <section className="bg-canvas">
      <div className="border-b border-line bg-surface">
        <div className="mx-auto max-w-6xl px-4 py-10 sm:px-6">
          <p className="section-eyebrow">Tentang</p>
          <h1 className="mt-2 font-display text-2xl font-semibold text-heading sm:text-3xl">Tentang SISTEMA</h1>
          <p className="mt-1 max-w-2xl text-sm text-ink-secondary">
            SISTEMA adalah sistem peminjaman alat yang membantu pengelolaan inventaris secara terstruktur dan transparan.
          </p>
        </div>
      </div>

      <div className="mx-auto grid max-w-6xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-[1.5fr_1fr]">
        <div className="card card-pad">
          <h2 className="font-display text-lg font-semibold text-heading">Tujuan sistem</h2>
          <p className="mt-3 text-sm leading-relaxed text-ink-secondary">
            SISTEMA dirancang untuk menggantikan pencatatan peminjaman alat secara manual. Seluruh data inventaris,
            pengajuan peminjaman, persetujuan petugas, dan pengembalian alat tercatat dalam satu sistem terpusat.
          </p>
        </div>

        <div className="card card-pad">
          <h2 className="font-display text-base font-semibold text-heading">Data sistem</h2>
          <dl className="mt-4 space-y-3 text-sm">
            <Row label="Total alat" value={tools} />
            <Row label="Kategori alat" value={categories} />
            <Row label="Alat tersedia" value={available} />
            <Row label="Total peminjaman" value={borrowings} />
          </dl>
        </div>
      </div>
    </section>
  );
}

function Row({ label, value }: { label: string; value: number }) {
  return (
    <div className="flex items-center justify-between border-b border-line pb-3 last:border-0 last:pb-0">
      <dt className="text-ink-secondary">{label}</dt>
      <dd className="font-semibold text-heading">{value}</dd>
    </div>
  );
}