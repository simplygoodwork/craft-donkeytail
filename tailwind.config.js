const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: ['./src/web/src/**/*.vue', './src/templates/**/*.twig'],
  theme: {
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
      }
    },
    extend: {
      boxShadow: {
        outline: 'inset 0 0 1px 1px var(--tw-ring-offset-blue-600)',
      },
      screens: {
        '2xl': '1600px',
      },
      lineHeight: {
        0: '0',
      },
      borderWidth: {
        3: '3px',
      },
    },
  },
  plugins: [],
};
