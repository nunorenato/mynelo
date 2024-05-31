import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
		'./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
		 './storage/framework/views/*.php',
		 './resources/views/**/*.blade.php',
		 "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
	],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
		forms,
        require("@tailwindcss/typography"),
		require("daisyui")
	],

    daisyui: {
        themes: [
            {
                nelotheme: {
                    "primary": "lightgray",
                    "secondary": "#E0CD3F",
                    "accent": "#E05E4C",
                    "neutral": "#3d4451",
                    "base-100": "#ffffff",
                }
            }
        ]
    },

    safelist: [
        'blur-lg',
        'blur-sm',
        'p-0',
        'w-14',
        {
            pattern: /badge.*/,
        },
        {
            pattern: /px-*/,
            variants: ['lg'],
        }
    ]
};
