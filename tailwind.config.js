const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
  purge: {
    content: [
      "./public/**/*.html",
      "./src/**/*.vue",
      "./assets/**/*.{vue,js,ts,jsx,tsx}",
      "./templates/**/*.{html,twig}",
    ]
  },
  content: [
    "./assets/**/*.{vue,js,ts,jsx,tsx}",
    "./templates/**/*.{html,twig}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Nunito', ...defaultTheme.fontFamily.sans],
      },
    }
  },
  plugins: [
    require("@tailwindcss/forms"),
    // require("@tailwindcss/typography")
  ],
}
