import Link from "next/link";
import { Boxes } from "lucide-react";
import ThemeToggle from "@/components/theme-toggle";
import { getSession } from "@/lib/session";
import { dashboardPath } from "@/lib/auth";

const NAV = [
  { label: "Beranda", href: "/" },
  { label: "Daftar Alat", href: "/alat" },
  { label: "Panduan", href: "/panduan" },
  { label: "Tentang", href: "/tentang" },
];

export default async function PublicLayout({ children }: { children: React.ReactNode }) {
  const session = await getSession();

  return (
    <div className="flex min-h-screen flex-col">
      <header className="sticky top-0 z-40 border-b border-line bg-surface/95 backdrop-blur">
        <div className="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
          <Link href="/" className="flex items-center gap-2.5">
            <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-navy text-white">
              <Boxes className="h-5 w-5" />
            </span>
            <span className="leading-tight">
              <span className="block font-display text-base font-bold tracking-tight text-heading">SISTEMA</span>
              <span className="block text-[11px] font-medium text-ink-secondary">Sistem Peminjaman Alat</span>
            </span>
          </Link>

          <nav className="hidden items-center gap-1 lg:flex">
            {NAV.map((item) => (
              <Link key={item.href} href={item.href} className="rounded-md px-3 py-2 text-sm font-medium text-ink-secondary transition-colors hover:text-heading">
                {item.label}
              </Link>
            ))}
          </nav>

          <div className="flex items-center gap-2">
            <ThemeToggle />
            {session ? (
              <Link href={dashboardPath(session.role)} className="btn-primary btn-sm">
                Dashboard
              </Link>
            ) : (
              <Link href="/login" className="btn-primary btn-sm">
                Login
              </Link>
            )}
          </div>
        </div>
      </header>

      <main className="flex-1">{children}</main>

      <footer className="mt-auto bg-navy text-white">
        <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6">
          <div className="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr]">
            <div>
              <div className="flex items-center gap-2.5">
                <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
                  <Boxes className="h-5 w-5" />
                </span>
                <span className="font-display text-lg font-bold">SISTEMA</span>
              </div>
              <p className="mt-4 max-w-sm text-sm leading-relaxed text-white/70">
                Platform digital untuk membantu pengelolaan inventaris, peminjaman, dan pengembalian alat secara terstruktur, transparan, dan efisien.
              </p>
            </div>

            <div>
              <h4 className="text-sm font-semibold text-white">Navigasi</h4>
              <ul className="mt-4 space-y-2.5 text-sm text-white/70">
                {NAV.map((item) => (
                  <li key={item.href}>
                    <Link href={item.href} className="transition-colors hover:text-white">
                      {item.label}
                    </Link>
                  </li>
                ))}
                <li>
                  <Link href="/login" className="transition-colors hover:text-white">
                    Login
                  </Link>
                </li>
              </ul>
            </div>

            <div>
              <h4 className="text-sm font-semibold text-white">Kontak</h4>
              <ul className="mt-4 space-y-2.5 text-sm text-white/70">
                <li>admin@sistema.test</li>
                <li>(021) 1234-5678</li>
                <li>Jl. Pendidikan No. 10, Jakarta</li>
              </ul>
            </div>
          </div>

          <div className="mt-10 flex flex-col gap-2 border-t border-white/10 pt-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
            <p>© {new Date().getFullYear()} SISTEMA. Seluruh hak cipta dilindungi.</p>
            <p>Sistem Peminjaman Alat · UKK Rekayasa Perangkat Lunak</p>
          </div>
        </div>
      </footer>
    </div>
  );
}