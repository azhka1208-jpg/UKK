import Link from "next/link";
import { clsx } from "clsx";

type Props = {
  label: string;
  value: React.ReactNode;
  icon: React.ReactNode;
  tone?: "navy" | "success" | "warning" | "danger" | "slate";
  hint?: string;
  href?: string;
};

const TONES: Record<string, string> = {
  navy: "bg-navy/10 text-navy dark:text-navy-200",
  success: "bg-success/10 text-success",
  warning: "bg-warning/10 text-[#a8781f]",
  danger: "bg-danger/10 text-danger",
  slate: "bg-ink/5 text-ink-secondary",
};

export default function StatCard({ label, value, icon, tone = "navy", hint, href }: Props) {
  const body = (
    <div className="flex items-start justify-between gap-4">
      <div className="min-w-0">
        <p className="text-xs font-medium uppercase tracking-wide text-ink-secondary">{label}</p>
        <p className="mt-2 font-display text-2xl font-semibold text-heading">{value}</p>
        {hint ? <p className="mt-1 text-xs text-ink-secondary">{hint}</p> : null}
      </div>
      <span className={clsx("flex h-10 w-10 shrink-0 items-center justify-center rounded-lg", TONES[tone])}>
        {icon}
      </span>
    </div>
  );

  if (href) {
    return (
      <Link href={href} className="card card-pad block transition-colors hover:border-navy/30">
        {body}
      </Link>
    );
  }

  return <div className="card card-pad">{body}</div>;
}