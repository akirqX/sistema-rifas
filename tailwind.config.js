import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import aspectRatio from '@tailwindcss/aspect-ratio';

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
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                heading: ['Archivo Black', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'primary-purple': 'var(--purple-medium)',
                'purple-dark': 'var(--purple-dark)',
                'purple-light': 'var(--purple-light)',
                'text-primary': 'var(--text-primary)',
                'text-secondary': 'var(--text-secondary)',
                'text-tertiary': 'var(--text-tertiary)',
                'bg-base': 'var(--bg-base)',
                'bg-secondary': 'var(--bg-secondary)',
                'border-base': 'var(--border-base)',
                'border-highlight': 'var(--border-highlight)',
            },
        },
    },

    plugins: [
        forms,
        aspectRatio,
    ],
};
