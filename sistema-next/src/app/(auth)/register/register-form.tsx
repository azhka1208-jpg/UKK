"use client";

import Link from "next/link";
import { useActionState } from "react";
import { UserPlus } from "lucide-react";
import { registerAction, type AuthState } from "../actions";

export default function RegisterForm() {
  const [state, formAction, pending] = useActionState(registerAction, {} as AuthState);

  return (
    <div>
      <div className="mb-6">
        <h1 className="font-display text-2xl font-semibold text-heading">Daftar akun peminjam</h1>
        <p className="mt-1 text-sm text-ink-secondary">Lengkapi data berikut untuk mengajukan peminjaman alat.</p>
      </div>

      <form action={formAction} className="card card-pad space-y-5">
        {state.error ? (
          <div className="rounded-lg border border-danger/20 bg-danger/10 px-4 py-3 text-sm text-danger">
            {state.error}
          </div>
        ) : null}

        <div>
          <label htmlFor="name" className="label">
            Nama Lengkap
          </label>
          <input id="name" name="name" type="text" required autoFocus className="input" placeholder="Nama lengkap" />
        </div>

        <div className="grid gap-4 sm:grid-cols-2">
          <div>
            <label htmlFor="username" className="label">
              Username
            </label>
            <input id="username" name="username" type="text" required className="input" placeholder="username" />
          </div>
          <div>
            <label htmlFor="phone" className="label">
              Nomor Telepon <span className="font-normal text-ink-secondary">(opsional)</span>
            </label>
            <input id="phone" name="phone" type="text" className="input" placeholder="08xxxxxxxxxx" />
          </div>
        </div>

        <div>
          <label htmlFor="email" className="label">
            Email
          </label>
          <input id="email" name="email" type="email" required className="input" placeholder="nama@email.com" />
        </div>

        <div>
          <label htmlFor="password" className="label">
            Kata Sandi
          </label>
          <input id="password" name="password" type="password" required className="input" placeholder="Minimal 8 karakter" />
        </div>

        <div>
          <label htmlFor="password_confirmation" className="label">
            Konfirmasi Kata Sandi
          </label>
          <input id="password_confirmation" name="password_confirmation" type="password" required className="input" placeholder="Ulangi kata sandi" />
        </div>

        <button type="submit" disabled={pending} className="btn-primary w-full">
          <UserPlus className="h-4 w-4" />
          {pending ? "Memproses..." : "Daftar"}
        </button>

        <p className="text-center text-sm text-ink-secondary">
          Sudah memiliki akun?{" "}
          <Link href="/login" className="link">
            Masuk di sini
          </Link>
        </p>
      </form>
    </div>
  );
}