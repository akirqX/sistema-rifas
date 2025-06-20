import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import aspectRatio from '@tailwindcss/aspect-ratio';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        "./resources/**/*.blade.php", // <-- ESSA LINHA É FUNDAMENTAL
    ],

    theme: {
        extend: {
            // Mapeamento das suas cores para o Tailwind
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
            // Mapeamento das suas fontes para o Tailwind
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                heading: ['Archivo Black', ...defaultTheme.fontFamily.sans],
            },
            // CORREÇÃO: Ensina o Tailwind a usar sua cor de borda personalizada
            borderColor: theme => ({
                ...theme('colors'),
                DEFAULT: 'var(--color-border)', // A mágica está aqui
                'primary-light': 'var(--color-primary-light)',
                'primary-dark': 'var(--color-primary-dark)',
            }),
        },
    },

    plugins: [
        forms,
        aspectRatio,
    ],
};
