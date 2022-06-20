import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
const path = require('path')

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],

  build: {
    manifest: true,
    lib: {
      name: 'trr-events-calendar',
      entry: './src/main.js'
    }
  },

  resolve: {
    alias: [
      {
        find: '@', replacement: path.resolve(__dirname, 'src'),
      },
      {
        find: '@styles', replacement: path.resolve(__dirname, 'src/assets/scss'),
      },
    ],
  },

  server: {
    host: true,
    https: {
      key: '/Users/moe/certificates/localhost+2-key.pem',
      cert: '/Users/moe/certificates/localhost+2.pem',
    }
  }
})
