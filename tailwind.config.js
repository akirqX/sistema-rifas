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
            // 👇👇👇 A CORREÇÃO ESTÁ AQUI 👇👇👇
            colors: {
                primary: {
                    light: 'var(--color-primary-light)',
                    dark: 'var(--color-primary-dark)',
                },
                accent: 'var(--color-accent)',
                'text-light': 'var(--color-text-light)',
                'text-muted': 'var(--color-text-muted)',
                'text-subtle': 'var(--color-text-subtle)',
                'bg-primary': 'var(--color-bg-primary)',
                'bg-secondary': 'var(--color-bg-secondary)',
                'bg-tertiary': 'var(--color-bg-tertiary)',
            },
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                heading: ['Archivo Black', ...defaultTheme.fontFamily.sans],
            },
            // Adicione outras customizações aqui se precisar
        },
    },

    // Não precisamos mais desativar o preflight
    // corePlugins: {
    //     preflight: false,
    // },

    plugins: [
        forms,
        aspectRatio,
    ],
};
