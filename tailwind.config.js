import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Vibrant Electric Blue Brand
                brand: {
                    50:  '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb', // Core Primary
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                // Refined Medical Green
                medical: {
                    50:  '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                },
                // Vivid Teal
                teal: {
                    50:  '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                },
                // Premium Urgent Red
                urgent: {
                    50:  '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                },
                // Airy Light Surface Palette
                surface: {
                    0:   '#ffffff',
                    50:  '#f4f7fe', // Very soft blue-gray for main background
                    100: '#e2e8f0', // Borders
                    200: '#cbd5e1',
                    300: '#94a3b8',
                    400: '#64748b', // Light text
                    500: '#475569',
                    600: '#334155', // Body text
                    700: '#1e293b',
                    800: '#0f172a', // Headings
                    900: '#020617',
                },
            },
            animation: {
                'fade-in-up':     'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'fade-in':        'fadeIn 0.5s ease-out forwards',
                'slide-in-right': 'slideInRight 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'pulse-slow':     'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'float':          'float 6s ease-in-out infinite',
            },
            keyframes: {
                fadeInUp: {
                    '0%':   { opacity: 0, transform: 'translateY(24px)' },
                    '100%': { opacity: 1, transform: 'translateY(0)' },
                },
                fadeIn: {
                    '0%':   { opacity: 0 },
                    '100%': { opacity: 1 },
                },
                slideInRight: {
                    '0%':   { opacity: 0, transform: 'translateX(-24px)' },
                    '100%': { opacity: 1, transform: 'translateX(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                }
            },
            boxShadow: {
                // Ultra soft, diffused shadows for premium feel
                'glass':   '0 8px 32px 0 rgba(31, 38, 135, 0.05)',
                'card':    '0 4px 20px -2px rgba(15, 23, 42, 0.05)',
                'card-md': '0 12px 30px -4px rgba(15, 23, 42, 0.08)',
                'card-lg': '0 24px 50px -12px rgba(15, 23, 42, 0.12)',
                'focus':   '0 0 0 4px rgba(37, 99, 235, 0.15)',
                'btn':     '0 4px 14px 0 rgba(37, 99, 235, 0.2)',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'hero-pattern': "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232563eb' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
            }
        },
    },

    plugins: [forms],
};
