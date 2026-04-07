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
            colors: {
                primary: '#1f5a7c',
                orange_cta: '#f97316',
                glass: 'rgba(255, 255, 255, 0.1)',
                border_sutil: 'rgba(255, 255, 255, 0.2)',
            },
            fontFamily: {
                sans: ['Inter', 'Roboto', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
