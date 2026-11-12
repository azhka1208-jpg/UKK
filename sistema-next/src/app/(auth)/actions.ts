"use server";

import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import bcrypt from "bcryptjs";
import { z } from "zod";
import { prisma } from "@/lib/prisma";
import { createSessionToken, dashboardPath, SESSION_COOKIE, SessionUser } from "@/lib/auth";
import { logActivity } from "@/lib/activity";

export type AuthState = { error?: string };

function setSessionCookie(token: string) {
  return {
    httpOnly: true,
    sameSite: "lax" as const,
    path: "/",
    maxAge: 60 * 60 * 24 * 7,
    secure: process.env.NODE_ENV === "production",
  };
}

export async function loginAction(_prev: AuthState, formData: FormData): Promise<AuthState> {
  const login = String(formData.get("login") ?? "").trim().toLowerCase();
  const password = String(formData.get("password") ?? "");
  const next = String(formData.get("next") ?? "");

  if (!login || !password) {
    return { error: "Email/username dan kata sandi wajib diisi." };
  }

  const user = await prisma.user.findFirst({
    where: { OR: [{ username: login }, { email: login }] },
  });

  if (!user || !(await bcrypt.compare(password, user.password))) {
    return { error: "Email/username atau kata sandi tidak sesuai." };
  }

  if (!user.isActive) {
    return { error: "Akun Anda sedang tidak aktif. Hubungi administrator." };
  }

  const token = await createSessionToken({
    id: user.id,
    name: user.name,
    username: user.username,
    role: user.role as SessionUser["role"],
  });

  const store = await cookies();
  store.set(SESSION_COOKIE, token, setSessionCookie(token));

  await logActivity(user.id, "login", "Masuk ke sistem.");

  redirect(next && next.startsWith("/") ? next : dashboardPath(user.role));
}

const registerSchema = z.object({
  name: z.string().min(2).max(100),
  username: z.string().min(3).max(40).regex(/^[a-zA-Z0-9_]+$/, "Username hanya huruf, angka, dan underscore."),
  email: z.string().email(),
  phone: z.string().max(30).optional().or(z.literal("")),
  password: z.string().min(8),
});

export async function registerAction(_prev: AuthState, formData: FormData): Promise<AuthState> {
  const parsed = registerSchema.safeParse({
    name: formData.get("name"),
    username: String(formData.get("username") ?? "").toLowerCase(),
    email: String(formData.get("email") ?? "").toLowerCase(),
    phone: formData.get("phone") ?? "",
    password: formData.get("password"),
  });

  if (!parsed.success) {
    return { error: parsed.error.issues[0]?.message ?? "Data tidak valid." };
  }

  const { name, username, email, phone, password } = parsed.data;

  if (password !== String(formData.get("password_confirmation") ?? "")) {
    return { error: "Konfirmasi kata sandi tidak cocok." };
  }

  const exists = await prisma.user.findFirst({ where: { OR: [{ username }, { email }] } });
  if (exists) {
    return { error: "Username atau email sudah terdaftar." };
  }

  const user = await prisma.user.create({
    data: {
      name,
      username,
      email,
      phone: phone || null,
      password: await bcrypt.hash(password, 10),
      role: "peminjam",
      isActive: true,
    },
  });

  const token = await createSessionToken({
    id: user.id,
    name: user.name,
    username: user.username,
    role: "peminjam",
  });

  const store = await cookies();
  store.set(SESSION_COOKIE, token, setSessionCookie(token));

  await logActivity(user.id, "register", "Mendaftar sebagai peminjam baru.", { type: "User", id: user.id });

  redirect("/peminjam");
}

export async function logoutAction() {
  const store = await cookies();
  const token = store.get(SESSION_COOKIE)?.value;
  if (token) {
    const { verifySessionToken } = await import("@/lib/auth");
    const user = await verifySessionToken(token);
    if (user) await logActivity(user.id, "logout", "Keluar dari sistem.");
  }
  store.delete(SESSION_COOKIE);
  redirect("/login");
}