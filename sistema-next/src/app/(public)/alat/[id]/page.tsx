import Link from "next/link";
import { notFound } from "next/navigation";
import { ChevronRight, Package, PlusCircle } from "lucide-react";
import { Badge } from "@/components/badge";
import { prisma } from "@/lib/prisma";
import { CONDITION_LABELS, CONDITION_TONES } from "@/lib/utils";
import { getSession } from "@/lib/session";

export default async function ToolDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const tool = await prisma.tool.findUnique({ where: { id: Number(id) }, include: { category: true } });
  if (!tool) notFound();

  const session = await getSession();
  const available = tool.status === "tersedia" && tool.stockAvailable > 0;
  const statusLabel = available ? "Tersedia" : tool.status === "perbaikan" ? "Perbaikan" : "Tidak Tersedia";
  const statusTone = available ? "badge-success" : tool.status === "perbaikan" ? "badge-warning" : "badge-danger";

  return (
    <section className="bg-canvas">
      <div className="border-b border-line bg-surface">
        <div className="mx-auto flex max-w-6xl items-center gap-2 px-4 py-6 text-sm text-ink-secondary sm:px-6">
          <Link href="/" className="hover:text-heading">Beranda</Link>
          <ChevronRight className="h-3.5 w-3.5" />
          <Link href="/alat" className="hover:text-heading">Daftar Alat</Link>
          <ChevronRight className="h-3.5 w-3.5" />
          <span className="text-heading">{tool.name}</span>
        </div>
      </div>

      <div className="mx-auto grid max-w-6xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-[1.1fr_1fr]">
        <div className="card flex aspect-[4/3] items-center justify-center overflow-hidden bg-ink/5">
          {tool.photo ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img src={tool.photo} alt={tool.name} className="h-full w-full object-cover" />
          ) : (
            <Package className="h-16 w-16 text-ink-secondary" />
          )}
        </div>

        <div className="card card-pad">
          <div className="flex flex-wrap items-center gap-2">
            <span className="rounded-full bg-accent px-3 py-1 text-xs font-medium text-heading">{tool.category?.name ?? "-"}</span>
            <Badge tone={statusTone} dot>{statusLabel}</Badge>
          </div>

          <h1 className="mt-3 font-display text-2xl font-semibold text-heading">{tool.name}</h1>
          <p className="mt-1 font-mono text-sm text-ink-secondary">{tool.code}</p>
          <p className="mt-4 text-sm leading-relaxed text-ink-secondary">{tool.description || "Belum ada deskripsi."}</p>

          <dl className="mt-6 grid grid-cols-2 gap-4 border-t border-line pt-5 text-sm">
            <div>
              <dt className="text-xs uppercase tracking-wide text-ink-secondary">Stok tersedia</dt>
              <dd className="mt-1 font-display text-lg font-semibold text-heading">
                {tool.stockAvailable} <span className="text-sm font-normal text-ink-secondary">/ {tool.stockTotal} unit</span>
              </dd>
            </div>
            <div>
              <dt className="text-xs uppercase tracking-wide text-ink-secondary">Kondisi</dt>
              <dd className="mt-1"><Badge tone={CONDITION_TONES[tool.condition]}>{CONDITION_LABELS[tool.condition]}</Badge></dd>
            </div>
            <div>
              <dt className="text-xs uppercase tracking-wide text-ink-secondary">Lokasi</dt>
              <dd className="mt-1 font-medium text-heading">{tool.location || "-"}</dd>
            </div>
          </dl>

          <div className="mt-6 border-t border-line pt-5">
            {session?.role === "peminjam" ? (
              available ? (
                <Link href={`/peminjam/peminjaman/ajukan?tool=${tool.id}`} className="btn-primary w-full sm:w-auto">
                  <PlusCircle className="h-4 w-4" />
                  Ajukan Peminjaman
                </Link>
              ) : (
                <button type="button" disabled className="btn-secondary w-full sm:w-auto">Alat tidak tersedia</button>
              )
            ) : session ? (
              <Link href="/" className="btn-secondary w-full sm:w-auto">Buka Dashboard</Link>
            ) : (
              <Link href="/login" className="btn-primary w-full sm:w-auto">Masuk untuk meminjam</Link>
            )}
          </div>
        </div>
      </div>
    </section>
  );
}