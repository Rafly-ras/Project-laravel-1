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
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#eff6ff",
                    100: "#dbeafe",
                    200: "#bfdbfe",
                    300: "#93c5fd",
                    400: "#60a5fa",
                    500: "#3b82f6",
                    600: "#2563eb",
                    700: "#1d4ed8",
                    800: "#1e40af",
                    900: "#1e3a8a",
                    950: "#172554",
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
            },
            borderRadius: {
                saas: "16px",
            },
        },
    },

    plugins: [forms],
};
