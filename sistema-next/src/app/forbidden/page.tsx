import Link from "next/link";
import { ShieldAlert } from "lucide-react";

export default function ForbiddenPage() {
  return (
    <div className="flex min-h-screen items-center justify-center bg-canvas px-4">
      <div className="card card-pad max-w-md text-center">
        <span className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-danger/10 text-danger">
          <ShieldAlert className="h-6 w-6" />
        </span>
        <h1 className="mt-4 font-display text-xl font-semibold text-heading">Akses ditolak</h1>
        <p className="mt-2 text-sm text-ink-secondary">
          Anda tidak memiliki hak akses untuk membuka halaman ini.
        </p>
        <div className="mt-5 flex justify-center gap-2">
          <Link href="/" className="btn-secondary">Beranda</Link>
          <Link href="/login" className="btn-primary">Masuk</Link>
        </div>
      </div>
    </div>
  );
}