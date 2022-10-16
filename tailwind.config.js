const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import("tailwindcss").Config} */
module.exports = {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Nunito", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "dark-blue": "#152938",
                "dark-gray": "#D3DCE4",
                "light-blue": "#27577B",
                "light-brown": "#32373C",
                "light-green": "#12B886",
            },
        },
    },

    plugins: [require("@tailwindcss/forms")],
};
