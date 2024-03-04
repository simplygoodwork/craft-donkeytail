import ViteRestart from 'vite-plugin-restart'
import mkcert from'vite-plugin-mkcert'
import vue from '@vitejs/plugin-vue'

export default ({ command }) => ({
  base: command === 'serve' ? '' : '/dist/',
  publicDir: 'non-existent-path',
  build: {
    manifest: true,
    outDir: './src/web/dist/',
    rollupOptions: {
      input: {
        donkeytail: './src/web/src/donkeytail.js',
      },
      output: {
        sourcemap: true
      },
    },
  },
  server: {
    host: '0.0.0.0',
    port: 3002,
    strictPort: true,
    https: true,
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
    },
  },
  plugins: [
    vue({ customElement: true }),
    mkcert(),
    ViteRestart({
      reload: ['./src/templates/**/*'],
    }),
  ],
})