import { prisma } from "./prisma";

export async function logActivity(
  userId: number | null,
  action: string,
  description?: string,
  subject?: { type: string; id: number }
) {
  try {
    await prisma.activityLog.create({
      data: {
        userId: userId ?? undefined,
        action,
        description,
        subjectType: subject?.type,
        subjectId: subject?.id,
      },
    });
  } catch {
    /* logging must never break the request */
  }
}

export async function notifyStaff(title: string, message: string, type = "info", url?: string) {
  const staff = await prisma.user.findMany({
    where: { role: { in: ["admin", "petugas"] }, isActive: true },
    select: { id: true },
  });
  if (staff.length === 0) return;
  await prisma.notification.createMany({
    data: staff.map((s) => ({ userId: s.id, title, message, type, url })),
  });
}

export async function notifyUser(userId: number, title: string, message: string, type = "info", url?: string) {
  await prisma.notification.create({ data: { userId, title, message, type, url } });
}