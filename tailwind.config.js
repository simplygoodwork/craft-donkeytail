// https://github.com/tailwindcss/tailwindcss/blob/master/stubs/defaultConfig.stub.js

module.exports = {
  theme: {
    container: {
      center: true,
      padding: '1rem',
    },
    boxShadow: theme => ({
      outline: `inset 0 0 1px 1px ${theme('colors.blue.600')}`,
    }),
    extend: {
      screens: {
        '2xl': '1600px',
      },
      spacing: {
        full: '100%',
      },
      lineHeight: {
        0: '0',
      },
      borderWidth: {
        3: '3px',
      },
      opacity: {
        90: '0.9',
      },
    },
  },
  variants: {
    backgroundColor: ['responsive', 'hover', 'focus', 'important'],
    flexWrap: ['responsive', 'hover', 'focus', 'important'],
    fontSize: ['responsive', 'important'],
    padding: ['responsive', 'important'],
    margin: ['responsive', 'important'],
  },
  plugins: [require('tailwindcss-important')()],
  purge: {
    content: ['./src/**/*.vue', './src/templates/**/*.twig'],
  },
}
