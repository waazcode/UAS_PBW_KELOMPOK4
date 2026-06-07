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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cheviot: '#F6F2E8',
                'grape-mist': '#C5C0C9',
                neptune: '#11425D',
                midnight: '#002233',
                isotonic: '#DDFF55',
                pacific: '#C0D6EA',
            },
        },
    },

    plugins: [
        forms({
            strategy: 'class',
        }),
    ],
};
