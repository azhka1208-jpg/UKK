import { prisma } from './prisma';

export const STATUS_LABELS: Record<string, string> = {
  menunggu: 'Menunggu Persetujuan',
  disetujui: 'Disetujui',
  ditolak: 'Ditolak',
  dipinjam: 'Sedang Dipinjam',
  selesai: 'Selesai',
  terlambat: 'Terlambat',
};

export const STATUS_TONES: Record<string, string> = {
  menunggu: 'badge-warning',
  disetujui: 'badge-info',
  dipinjam: 'badge-info',
  selesai: 'badge-success',
  ditolak: 'badge-danger',
  terlambat: 'badge-danger',
};

export const ACTIVE_STATUSES = ['disetujui', 'dipinjam', 'terlambat'];

export const CONDITION_LABELS: Record<string, string> = {
  baik: 'Baik',
  rusak_ringan: 'Rusak Ringan',
  rusak_berat: 'Rusak Berat',
};

export const CONDITION_TONES: Record<string, string> = {
  baik: 'badge-success',
  rusak_ringan: 'badge-warning',
  rusak_berat: 'badge-danger',
};

export function formatDate(date: Date | string | null | undefined): string {
  if (!date) return '-';
  const d = typeof date === 'string' ? new Date(date) : date;
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

export function formatDateTime(date: Date | string | null | undefined): string {
  if (!date) return '-';
  const d = typeof date === 'string' ? new Date(date) : date;
  return d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

export function startOfToday(): Date {
  const d = new Date();
  d.setHours(0, 0, 0, 0);
  return d;
}

export function daysBetween(a: Date, b: Date): number {
  return Math.floor((a.getTime() - b.getTime()) / 86_400_000);
}

export function isLate(dueDate: Date | string, status: string): boolean {
  if (!ACTIVE_STATUSES.includes(status)) return false;
  const due = typeof dueDate === 'string' ? new Date(dueDate) : dueDate;
  return due.getTime() < startOfToday().getTime();
}

export function lateDays(dueDate: Date | string, status: string): number {
  if (!ACTIVE_STATUSES.includes(status)) return 0;
  const due = typeof dueDate === 'string' ? new Date(dueDate) : dueDate;
  const diff = daysBetween(startOfToday(), new Date(due));
  return diff > 0 ? diff : 0;
}

export async function settingValue(key: string, fallback = ''): Promise<string> {
  const setting = await prisma.setting.findUnique({ where: { key } });
  return setting?.value ?? fallback;
}

export async function finePerDay(): Promise<number> {
  return Number(await settingValue('fine_per_day', '0')) || 0;
}

export async function fineEnabled(): Promise<boolean> {
  return (await settingValue('fine_enabled', '0')) === '1';
}

export async function maxLoanDays(): Promise<number> {
  return Number(await settingValue('max_loan_days', '14')) || 14;
}

export function generateCode(prefix: string, lastCode: string | null | undefined): string {
  const year = new Date().getFullYear();
  let sequence = 1;
  if (lastCode) {
    sequence = parseInt(lastCode.slice(-4), 10) + 1;
  }
  return `${prefix}-${year}-${String(sequence).padStart(4, '0')}`;
}