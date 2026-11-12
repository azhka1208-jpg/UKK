import { prisma } from "@/lib/prisma";
import ToolCard from "@/components/tool-card";

export default async function AlatPage({
  searchParams,
}: {
  searchParams: Promise<{ q?: string; category?: string; status?: string }>;
}) {
  const p = await searchParams;

  const where: Record<string, unknown> = {};
  if (p.q) {
    where.OR = [{ name: { contains: p.q } }, { code: { contains: p.q } }, { description: { contains: p.q } }];
  }
  if (p.category) where.categoryId = Number(p.category);
  if (p.status === "tersedia") {
    where.status = "tersedia";
    where.stockAvailable = { gt: 0 };
  } else if (p.status === "tidak_tersedia") {
    where.OR = [{ status: { not: "tersedia" } }, { stockAvailable: { lte: 0 } }];
  }

  const [tools, categories] = await Promise.all([
    prisma.tool.findMany({ where, include: { category: true }, orderBy: { name: "asc" } }),
    prisma.category.findMany({ orderBy: { name: "asc" } }),
  ]);

  return (
    <section className="bg-canvas">
      <div className="border-b border-line bg-surface">
        <div className="mx-auto max-w-6xl px-4 py-10 sm:px-6">
          <p className="section-eyebrow">Katalog Inventaris</p>
          <h1 className="mt-2 font-display text-2xl font-semibold text-heading sm:text-3xl">Daftar Alat</h1>
          <p className="mt-1 max-w-2xl text-sm text-ink-secondary">
            Telusuri alat yang tersedia beserta kategori, kondisi, dan status ketersediaannya.
          </p>
        </div>
      </div>

      <div className="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <form method="GET" className="card card-pad">
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_auto]">
            <div>
              <label className="label" htmlFor="q">Pencarian</label>
              <input id="q" name="q" defaultValue={p.q ?? ""} placeholder="Nama atau kode alat" className="input" />
            </div>
            <div>
              <label className="label" htmlFor="category">Kategori</label>
              <select id="category" name="category" defaultValue={p.category ?? ""} className="input">
                <option value="">Semua kategori</option>
                {categories.map((c) => (
                  <option key={c.id} value={c.id}>{c.name}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="label" htmlFor="status">Status</label>
              <select id="status" name="status" defaultValue={p.status ?? ""} className="input">
                <option value="">Semua status</option>
                <option value="tersedia">Tersedia</option>
                <option value="tidak_tersedia">Tidak tersedia</option>
              </select>
            </div>
            <div className="flex items-end gap-2">
              <button type="submit" className="btn-primary">Terapkan</button>
            </div>
          </div>
        </form>

        <p className="mt-6 text-sm text-ink-secondary">{tools.length} alat ditemukan</p>

        <div className="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {tools.map((tool) => (
            <ToolCard key={tool.id} tool={tool} href={`/alat/${tool.id}`} />
          ))}
        </div>

        {tools.length === 0 ? (
          <div className="card mt-4 p-10 text-center text-sm text-ink-secondary">Alat tidak ditemukan. Coba ubah filter.</div>
        ) : null}
      </div>
    </section>
  );
}