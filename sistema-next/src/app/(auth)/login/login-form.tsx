"use client";

import Link from "next/link";
import { useActionState } from "react";
import { LogIn } from "lucide-react";
import { loginAction, type AuthState } from "../actions";

export default function LoginForm({ next }: { next?: string }) {
  const [state, formAction, pending] = useActionState(loginAction, {} as AuthState);

  return (
    <div>
      <div className="mb-6">
        <h1 className="font-display text-2xl font-semibold text-heading">Masuk ke sistem</h1>
        <p className="mt-1 text-sm text-ink-secondary">Gunakan email atau username yang terdaftar.</p>
      </div>

      <form action={formAction} className="card card-pad space-y-5">
        <input type="hidden" name="next" value={next ?? ""} />

        {state.error ? (
          <div className="rounded-lg border border-danger/20 bg-danger/10 px-4 py-3 text-sm text-danger">
            {state.error}
          </div>
        ) : null}

        <div>
          <label htmlFor="login" className="label">
            Email atau Username
          </label>
          <input id="login" name="login" type="text" required autoFocus className="input" placeholder="nama@email.com atau username" />
        </div>

        <div>
          <label htmlFor="password" className="label">
            Kata Sandi
          </label>
          <input id="password" name="password" type="password" required className="input" placeholder="Masukkan kata sandi" />
        </div>

        <label className="flex items-center gap-2 text-sm text-ink-secondary">
          <input type="checkbox" name="remember" value="1" className="rounded border-line" />
          Ingat saya
        </label>

        <button type="submit" disabled={pending} className="btn-primary w-full">
          <LogIn className="h-4 w-4" />
          {pending ? "Memproses..." : "Masuk"}
        </button>

        <p className="text-center text-sm text-ink-secondary">
          Belum memiliki akun?{" "}
          <Link href="/register" className="link">
            Daftar sebagai peminjam
          </Link>
        </p>
      </form>
    </div>
  );
}