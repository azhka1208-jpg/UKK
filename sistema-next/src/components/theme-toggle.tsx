"use client";

import { Moon, Sun } from "lucide-react";
import { useCallback, useRef } from "react";

type Props = {
  className?: string;
};

export default function ThemeToggle({ className = "btn-icon" }: Props) {
  const ref = useRef<HTMLButtonElement>(null);
  const transitioning = useRef(false);

  const applyTheme = (theme: string) => {
    const root = document.documentElement;
    root.classList.toggle("dark", theme === "dark");
    try {
      localStorage.setItem("theme", theme);
    } catch {
      /* ignore */
    }
  };

  const change = useCallback(() => {
    if (transitioning.current) return;

    const root = document.documentElement;
    const next = root.classList.contains("dark") ? "light" : "dark";
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const supports = typeof (document as unknown as { startViewTransition?: unknown }).startViewTransition === "function";

    if (!supports || reduced) {
      applyTheme(next);
      return;
    }

    const btn = ref.current;
    if (btn) {
      const rect = btn.getBoundingClientRect();
      const x = rect.left + rect.width / 2;
      const y = rect.top + rect.height / 2;
      const vw = window.innerWidth;
      const vh = window.innerHeight;
      const radius = Math.hypot(Math.max(x, vw - x), Math.max(y, vh - y));
      root.style.setProperty("--theme-origin-x", `${x}px`);
      root.style.setProperty("--theme-origin-y", `${y}px`);
      root.style.setProperty("--theme-radius", `${radius}px`);
    }

    root.classList.add("sistema-theme-transition");
    transitioning.current = true;

    const finish = () => {
      transitioning.current = false;
      root.classList.remove("sistema-theme-transition");
    };

    try {
      const transition = (
        document as unknown as {
          startViewTransition: (cb: () => void) => { finished?: Promise<void> };
        }
      ).startViewTransition(() => applyTheme(next));

      if (transition.finished?.finally) {
        transition.finished.finally(finish);
      } else if (transition.finished?.then) {
        transition.finished.then(finish, finish);
      } else {
        window.setTimeout(finish, 1800);
      }
    } catch {
      applyTheme(next);
      finish();
    }

    window.setTimeout(finish, 2400);
  }, []);

  return (
    <button
      ref={ref}
      type="button"
      onClick={change}
      className={className}
      title="Ganti tema"
      aria-label="Ganti tema"
    >
      <span className="theme-sun">
        <Sun className="h-5 w-5" />
      </span>
      <span className="theme-moon">
        <Moon className="h-5 w-5" />
      </span>
    </button>
  );
}