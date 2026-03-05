import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#f5f7ff",
                    100: "#ebf0ff",
                    200: "#d6e0ff",
                    300: "#adc2ff",
                    400: "#85a3ff",
                    500: "#5c85ff",
                    600: "#4f72ff",
                    700: "#425fff",
                    800: "#354cff",
                    900: "#2839ff",
                    950: "#1b26ff",
                },
                enterprise: {
                    bg: "#F8FAFC",
                    sidebar: "#0F172A",
                    accent: "#4F46E5",
                },
                saas: {
                    bg: "#F4F8FF",
                    card: "#FFFFFF",
                    blue: "#2563EB",
                    navy: "#0F172A",
                    slate: "#475569",
                    soft: "#DBEAFE",
                    success: "#16A34A",
                    danger: "#DC2626",
                    border: "#E2E8F0",
                },
            },
            boxShadow: {
                saas: "0 4px 12px rgba(0, 0, 0, 0.05)",
                premium: "0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 6px 10px -5px rgba(0, 0, 0, 0.02)",
            },
            borderRadius: {
                saas: "16px",
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'fade-in-down': 'fadeInDown 0.5s ease-out',
                'slide-in-right': 'slideInRight 0.3s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { transform: 'translateX(20px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
