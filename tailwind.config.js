const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/views/pos/**/*.blade.php",
  ],
  theme: {
    extend: {
        fontFamily: {
            sans: ['Inter', ...defaultTheme.fontFamily.sans],
        },
        colors: {
            primary: {
                DEFAULT: '#4f46e5',
                '50': '#eef2ff',
                '100': '#e0e7ff',
                '200': '#c7d2fe',
                '300': '#a5b4fc',
                '400': '#818cf8',
                '500': '#6366f1',
                '600': '#4f46e5',
                '700': '#4338ca',
                '800': '#3730a3',
                '900': '#312e81',
            },
            secondary: {
                DEFAULT: '#1f2937',
                '50': '#f9fafb',
                '100': '#f3f4f6',
                '200': '#e5e7eb',
                '300': '#d1d5db',
                '400': '#9ca3af',
                '500': '#6b7280',
                '600': '#4b5563',
                '700': '#374151',
                '800': '#1f2937',
                '900': '#111827',
            },
            accent: {
                DEFAULT: '#10b981',
                '50': '#f0fdf4',
                '100': '#dcfce7',
                '200': '#bbf7d0',
                '300': '#86efac',
                '400': '#4ade80',
                '500': '#22c55e',
                '600': '#16a34a',
                '700': '#15803d',
                '800': '#166534',
                '900': '#14532d',
            }
        }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

