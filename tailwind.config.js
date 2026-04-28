import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                serif: ["Playfair Display", ...defaultTheme.fontFamily.serif],
            },
            colors: {
                "luxury-nude": "#F5F1EA",
                "luxury-gold": "#C5A059",
                "luxury-charcoal": "#1A1A1A",
                "luxury-cream": "#FDFBF7",
                "luxury-clay": "#EAE3D5",
            },
        },
    },

    plugins: [forms],
};
