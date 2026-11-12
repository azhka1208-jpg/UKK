import { AlertCircle, AlertTriangle, CheckCircle2, Info } from "lucide-react";

type Tone = "success" | "danger" | "warning" | "info";

const TONES: Record<Tone, { className: string; Icon: typeof Info }> = {
  success: { className: "bg-success/10 text-success border-success/20", Icon: CheckCircle2 },
  danger: { className: "bg-danger/10 text-danger border-danger/20", Icon: AlertCircle },
  warning: { className: "bg-warning/10 text-[#a8781f] border-warning/20", Icon: AlertTriangle },
  info: { className: "bg-accent text-heading border-navy/15", Icon: Info },
};

export default function Alert({
  tone = "info",
  children,
}: {
  tone?: Tone;
  children: React.ReactNode;
}) {
  const { className, Icon } = TONES[tone];
  return (
    <div className={`flex items-start gap-3 rounded-lg border px-4 py-3 text-sm ${className}`}>
      <Icon className="mt-0.5 h-4 w-4 shrink-0" />
      <p className="flex-1">{children}</p>
    </div>
  );
}