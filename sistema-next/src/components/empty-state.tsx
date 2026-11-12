type Props = {
  title: string;
  description?: string;
  icon?: React.ReactNode;
  action?: React.ReactNode;
};

export default function EmptyState({ title, description, icon, action }: Props) {
  return (
    <div className="empty-state">
      <span className="flex h-12 w-12 items-center justify-center rounded-xl bg-accent text-heading">
        {icon}
      </span>
      <p className="mt-4 font-display text-base font-semibold text-heading">{title}</p>
      {description ? <p className="mt-1 max-w-sm text-sm text-ink-secondary">{description}</p> : null}
      {action ? <div className="mt-5">{action}</div> : null}
    </div>
  );
}