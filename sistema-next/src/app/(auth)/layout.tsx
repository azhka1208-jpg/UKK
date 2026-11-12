import Link from "next/link";
import { Boxes, History, PackageCheck, ShieldCheck } from "lucide-react";
import ThemeToggle from "@/components/theme-toggle";

export default function AuthLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="grid min-h-screen lg:grid-cols-2">
      <div className="fixed right-4 top-4 z-50">
        <ThemeToggle />
      </div>

      <aside className="relative hidden flex-col justify-between overflow-hidden bg-navy p-10 text-white lg:flex">
        <div className="relative z-10 flex items-center gap-2.5">
          <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
            <Boxes className="h-5 w-5" />
          </span>
          <div className="leading-tight">
            <p className="font-display text-base font-bold">SISTEMA</p>
            <p className="text-[11px] text-white/60">Sistem Peminjaman Alat</p>
          </div>
        </div>

        <div className="relative z-10 max-w-md">
          <h1 className="font-display text-3xl font-bold leading-tight text-white">
            Kelola peminjaman alat secara terstruktur.
          </h1>
          <p className="mt-4 text-sm leading-relaxed text-white/70">
            Satu sistem untuk inventaris, pengajuan peminjaman, persetujuan petugas, dan pengembalian alat.
          </p>

          <ul className="mt-8 space-y-4 text-sm">
            {[
              { Icon: PackageCheck, text: "Pantau stok alat secara aktual." },
              { Icon: ShieldCheck, text: "Hak akses terpisah untuk setiap peran." },
              { Icon: History, text: "Riwayat peminjaman tersimpan rapi." },
            ].map(({ Icon, text }) => (
              <li key={text} className="flex items-center gap-3">
                <span className="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10">
                  <Icon className="h-4 w-4" />
                </span>
                <span className="text-white/80">{text}</span>
              </li>
            ))}
          </ul>
        </div>

        <p className="relative z-10 text-xs text-white/50">© {new Date().getFullYear()} SISTEMA · UKK Rekayasa Perangkat Lunak</p>

        <div className="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/5" />
        <div className="pointer-events-none absolute -bottom-32 -right-10 h-80 w-80 rounded-full bg-white/5" />
      </aside>

      <main className="flex items-center justify-center px-4 py-10 sm:px-6">
        <div className="w-full max-w-md">
          <div className="mb-8 flex items-center gap-2.5 lg:hidden">
            <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-navy text-white">
              <Boxes className="h-5 w-5" />
            </span>
            <div className="leading-tight">
              <p className="font-display text-base font-bold text-heading">SISTEMA</p>
              <p className="text-[11px] text-ink-secondary">Sistem Peminjaman Alat</p>
            </div>
          </div>

          {children}

          <p className="mt-8 text-center text-xs text-ink-secondary">
            © {new Date().getFullYear()} SISTEMA ·{" "}
            <Link href="/" className="link">
              Kembali ke beranda
            </Link>
          </p>
        </div>
      </main>
    </div>
  );
}