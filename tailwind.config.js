const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
  purge: { content: ["./public/**/*.html", "./src/**/*.vue"] },
  content: [],
  theme: {
    extend: {},
  },
  // plugins: [require("@tailwindcss/forms"), require("@tailwindcss/typography")],
}
