import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

const renderIcons = () => createIcons({ icons });

if (document.readyState !== 'loading') {
    renderIcons();
} else {
    document.addEventListener('DOMContentLoaded', renderIcons);
}

document.addEventListener('alpine:initialized', renderIcons);

window.renderIcons = renderIcons;

/* ------------------------------------------------------------------
 | Theme (light / dark) + circular reveal transition
 |------------------------------------------------------------------ */
const root = document.documentElement;
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const supportsViewTransition = typeof document.startViewTransition === 'function';
const THEME_TRANSITION_DURATION = 1700;

let themeTransitioning = false;

const applyTheme = (theme) => {
    root.classList.toggle('dark', theme === 'dark');
    try {
        localStorage.setItem('theme', theme);
    } catch (e) {
        /* ignore storage errors */
    }
};

/**
 * Simpan posisi tengah tombol ke CSS variable dan hitung radius
 * hingga sudut terjauh viewport.
 */
const setThemeOrigin = (button) => {
    const rect = button.getBoundingClientRect();
    const x = rect.left + rect.width / 2;
    const y = rect.top + rect.height / 2;
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    const radius = Math.hypot(
        Math.max(x, viewportWidth - x),
        Math.max(y, viewportHeight - y)
    );

    root.style.setProperty('--theme-origin-x', `${x}px`);
    root.style.setProperty('--theme-origin-y', `${y}px`);
    root.style.setProperty('--theme-radius', `${radius}px`);
};

const changeTheme = (button) => {
    const nextTheme = root.classList.contains('dark') ? 'light' : 'dark';

    if (themeTransitioning) {
        return;
    }

    // Fallback: browser tanpa View Transition API atau pengguna memilih
    // reduced motion -> ganti tema tanpa animasi.
    if (!supportsViewTransition || reducedMotion.matches) {
        applyTheme(nextTheme);
        return;
    }

    setThemeOrigin(button);
    root.classList.add('sistema-theme-transition');
    themeTransitioning = true;

    const finish = () => {
        themeTransitioning = false;
        root.classList.remove('sistema-theme-transition');
    };

    let transition;

    try {
        transition = document.startViewTransition(() => {
            applyTheme(nextTheme);
        });
    } catch (e) {
        applyTheme(nextTheme);
        finish();
        return;
    }

    if (transition && transition.finished) {
        if (typeof transition.finished.finally === 'function') {
            transition.finished.finally(finish);
        } else {
            transition.finished.then(finish, finish);
        }
    } else {
        window.setTimeout(finish, THEME_TRANSITION_DURATION + 100);
    }

    // Pengaman: pastikan state tidak macet bila event transition tidak terpicu.
    window.setTimeout(finish, THEME_TRANSITION_DURATION + 600);
};

const bindThemeToggles = () => {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        if (button.dataset.themeBound === '1') {
            return;
        }
        button.dataset.themeBound = '1';
        button.addEventListener('click', (event) => {
            changeTheme(event.currentTarget || button);
        });
    });
};

if (document.readyState !== 'loading') {
    bindThemeToggles();
} else {
    document.addEventListener('DOMContentLoaded', bindThemeToggles);
}