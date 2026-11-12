"use client";

import { AlertTriangle } from "lucide-react";
import { useState } from "react";

type Props = {
  action: (formData: FormData) => void | Promise<void>;
  id: number | string;
  title?: string;
  message?: string;
  label?: string;
  className?: string;
};

export default function ConfirmButton({
  action,
  id,
  title = "Konfirmasi tindakan",
  message = "Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.",
  label = "Hapus",
  className = "btn-danger btn-sm",
}: Props) {
  const [open, setOpen] = useState(false);

  return (
    <>
      <button type="button" onClick={() => setOpen(true)} className={className}>
        {label}
      </button>

      {open ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
          <div className="absolute inset-0 bg-ink/45" onClick={() => setOpen(false)} />
          <div className="relative w-full max-w-md rounded-xl bg-surface p-5 shadow-[var(--shadow-pop)]">
            <div className="flex items-start gap-3">
              <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger/10 text-danger">
                <AlertTriangle className="h-5 w-5" />
              </span>
              <div>
                <h3 className="font-display text-base font-semibold text-heading">{title}</h3>
                <p className="mt-1 text-sm text-ink-secondary">{message}</p>
              </div>
            </div>

            <form action={action} className="mt-6 flex justify-end gap-2">
              <input type="hidden" name="id" value={id} />
              <button type="button" className="btn-secondary" onClick={() => setOpen(false)}>
                Batal
              </button>
              <button type="submit" className="btn-danger">
                {label}
              </button>
            </form>
          </div>
        </div>
      ) : null}
    </>
  );
}