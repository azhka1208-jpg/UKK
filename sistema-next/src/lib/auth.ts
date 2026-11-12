import { SignJWT, jwtVerify } from 'jose';

export type SessionUser = {
  id: number;
  name: string;
  username: string;
  role: 'admin' | 'petugas' | 'peminjam';
};

const COOKIE = 'sistema_session';
const secret = new TextEncoder().encode(
  process.env.AUTH_SECRET || 'sistema-dev-secret-change-me-please-0123456789abcdef'
);

export async function createSessionToken(user: SessionUser): Promise<string> {
  return new SignJWT({ ...user })
    .setProtectedHeader({ alg: 'HS256' })
    .setIssuedAt()
    .setExpirationTime('7d')
    .sign(secret);
}

export async function verifySessionToken(token: string): Promise<SessionUser | null> {
  try {
    const { payload } = await jwtVerify(token, secret);
    if (
      typeof payload.id === 'number' &&
      typeof payload.username === 'string' &&
      typeof payload.role === 'string' &&
      typeof payload.name === 'string'
    ) {
      return {
        id: payload.id,
        name: payload.name,
        username: payload.username,
        role: payload.role as SessionUser['role'],
      };
    }
    return null;
  } catch {
    return null;
  }
}

export const SESSION_COOKIE = COOKIE;

export function dashboardPath(role: string): string {
  if (role === 'admin') return '/admin';
  if (role === 'petugas') return '/petugas';
  return '/peminjam';
}