/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                navy: {
                    DEFAULT: '#1E3A5F',
                    50: '#EEF3F9',
                    100: '#D9E4F0',
                    200: '#B3C8E0',
                    300: '#8CACD0',
                    400: '#5E85AE',
                    500: '#476B91',
                    600: '#385675',
                    700: '#2A435C',
                    800: '#1E3A5F',
                    900: '#16293F',
                },
                accent: {
                    DEFAULT: 'rgb(var(--c-accent) / <alpha-value>)',
                    dark: 'rgb(var(--c-accent-dark) / <alpha-value>)',
                },
                ink: {
                    DEFAULT: 'rgb(var(--c-ink) / <alpha-value>)',
                    secondary: 'rgb(var(--c-ink-secondary) / <alpha-value>)',
                },
                heading: 'rgb(var(--c-heading) / <alpha-value>)',
                canvas: 'rgb(var(--c-canvas) / <alpha-value>)',
                surface: 'rgb(var(--c-surface) / <alpha-value>)',
                line: 'rgb(var(--c-line) / <alpha-value>)',
                success: '#198754',
                warning: '#E6A23C',
                danger: '#DC5252',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': ['0.6875rem', { lineHeight: '1rem' }],
            },
            boxShadow: {
                card: '0 1px 2px rgba(37, 50, 71, 0.04), 0 1px 3px rgba(37, 50, 71, 0.06)',
                pop: '0 10px 30px -12px rgba(30, 58, 95, 0.25)',
            },
            borderRadius: {
                xl: '0.75rem',
                '2xl': '1rem',
            },
        },
    },
    plugins: [require('@tailwindcss/forms')({ strategy: 'class' })],
};