import Link from "next/link";
import { ArrowRight, Package } from "lucide-react";
import { Badge } from "./badge";
import { CONDITION_LABELS, CONDITION_TONES } from "@/lib/utils";

type ToolCardTool = {
  id: number;
  code: string;
  name: string;
  location: string | null;
  condition: string;
  status: string;
  stockAvailable: number;
  stockTotal: number;
  photo: string | null;
  category?: { name: string } | null;
};

export default function ToolCard({ tool, href }: { tool: ToolCardTool; href: string }) {
  const available = tool.status === "tersedia" && tool.stockAvailable > 0;
  const statusLabel = available ? "Tersedia" : tool.status === "perbaikan" ? "Perbaikan" : "Tidak Tersedia";
  const statusTone = available ? "badge-success" : tool.status === "perbaikan" ? "badge-warning" : "badge-danger";

  return (
    <article className="card group flex flex-col overflow-hidden">
      <Link href={href} className="block overflow-hidden bg-ink/5">
        {tool.photo ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={tool.photo}
            alt={tool.name}
            className="h-40 w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
          />
        ) : (
          <div className="flex h-40 w-full items-center justify-center text-ink-secondary">
            <Package className="h-10 w-10" />
          </div>
        )}
      </Link>

      <div className="flex flex-1 flex-col p-4">
        <div className="flex items-center justify-between gap-2">
          <span className="text-xs font-medium text-ink-secondary">{tool.category?.name ?? "-"}</span>
          <Badge tone={statusTone} dot>
            {statusLabel}
          </Badge>
        </div>

        <Link href={href} className="mt-2 font-display text-base font-semibold leading-snug text-heading hover:underline underline-offset-2">
          {tool.name}
        </Link>

        <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-ink-secondary">
          <span className="font-mono">{tool.code}</span>
          <span>·</span>
          <span>
            Stok {tool.stockAvailable}/{tool.stockTotal}
          </span>
        </div>

        <div className="mt-3 flex items-center gap-2">
          <Badge tone={CONDITION_TONES[tool.condition] ?? "badge-neutral"}>
            {CONDITION_LABELS[tool.condition] ?? tool.condition}
          </Badge>
          {tool.location ? <span className="text-xs text-ink-secondary">{tool.location}</span> : null}
        </div>

        <div className="mt-4 flex-1" />

        <Link href={href} className="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-heading hover:underline underline-offset-2">
          Lihat detail
          <ArrowRight className="h-4 w-4" />
        </Link>
      </div>
    </article>
  );
}