const postcssPresetEnv = require('postcss-preset-env')
let production = process.env.NODE_ENV === 'production'
// defaultExtractor: content => content.match(/[\w-/:!]+(?<!:)/g) || [],

module.exports = {
  plugins: [
    require('postcss-import'),
    require('tailwindcss'),
    postcssPresetEnv({
      stage: 3,
      features: {
        'nesting-rules': true,
      },
    }),
    require('autoprefixer'),
  ],
}
