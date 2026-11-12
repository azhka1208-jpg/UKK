import { clsx } from "clsx";

type BadgeProps = {
  children: React.ReactNode;
  tone?: string;
  dot?: boolean;
  className?: string;
};

export function Badge({ children, tone = "badge-neutral", dot = false, className }: BadgeProps) {
  return (
    <span className={clsx("badge", tone, className)}>
      {dot ? <span className="h-1.5 w-1.5 rounded-full bg-current" /> : null}
      {children}
    </span>
  );
}