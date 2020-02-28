const { spacing } = require('tailwindcss/defaultTheme')

module.exports = {
  prefix: '',
  important: true,
  theme: {
    extend: {},
  },
  variants: {},
  plugins: [
    require('tailwindcss-spaced-items')({ values: spacing })
  ]
}
