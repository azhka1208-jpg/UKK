import { NextResponse, type NextRequest } from "next/server";
import { SESSION_COOKIE, verifySessionToken } from "@/lib/auth";

const ROLE_PREFIX: Record<string, string> = {
  "/admin": "admin",
  "/petugas": "petugas",
  "/peminjam": "peminjam",
};

export async function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl;
  const token = request.cookies.get(SESSION_COOKIE)?.value;
  const user = token ? await verifySessionToken(token) : null;

  const needsAuth =
    pathname.startsWith("/admin") ||
    pathname.startsWith("/petugas") ||
    pathname.startsWith("/peminjam") ||
    pathname.startsWith("/profil") ||
    pathname.startsWith("/notifikasi");

  if (!user && needsAuth) {
    const url = request.nextUrl.clone();
    url.pathname = "/login";
    url.searchParams.set("next", pathname);
    return NextResponse.redirect(url);
  }

  if (user) {
    for (const [prefix, role] of Object.entries(ROLE_PREFIX)) {
      if (pathname.startsWith(prefix) && user.role !== role) {
        const url = request.nextUrl.clone();
        url.pathname = "/forbidden";
        url.search = "";
        return NextResponse.redirect(url);
      }
    }
    if ((pathname === "/login" || pathname === "/register")) {
      const url = request.nextUrl.clone();
      url.pathname = user.role === "admin" ? "/admin" : user.role === "petugas" ? "/petugas" : "/peminjam";
      url.search = "";
      return NextResponse.redirect(url);
    }
  }

  return NextResponse.next();
}

export const config = {
  matcher: ["/admin/:path*", "/petugas/:path*", "/peminjam/:path*", "/profil/:path*", "/notifikasi/:path*", "/login", "/register"],
};