/* eslint-disable no-plusplus */
import react from '@vitejs/plugin-react'
import path from 'path'
import { defineConfig } from 'vite'

let chunkCount = 0

export default defineConfig(({ mode }) => ({

  plugins: [
    react(),
  ],

  root: 'src',
  base: mode === 'development' ? '/' : '',
  build: {
    outDir: '../../assets',
    emptyOutDir: true,

    // emit manifest so PHP can find the hashed files
    manifest: true,

    target: 'es2015',
    // minify: 'terser',

    // sourcemap: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'src/main.jsx'),
      output: {
        entryFileNames: 'main.js',
        compact: true,
        validate: true,
        generatedCode: {
          arrowFunctions: true,
          // objectShorthand: true
        },
        chunkFileNames: () => `bi-${hash()}-${chunkCount++}.js`,
        assetFileNames: (fInfo) => {
          const pathArr = fInfo.name.split('/')
          const fileName = pathArr[pathArr.length - 1]

          if (fileName === 'main.css') {
            return 'main.css'
          }
          if (fileName === 'logo.svg') {
            return 'logo.svg'
          }

          return `bi-${hash()}-${chunkCount++}.[ext]`
        },
      },
    },
    commonjsOptions: { transformMixedEsModules: true },
  },

  server: {
    origin: 'http://localhost:3000',
    // required to load scripts from custom host
    cors: true,
    // we need a strict port to match on PHP side
    strictPort: true,
    port: 3000,
    hmr: { host: 'localhost' },
    commonjsOptions: { transformMixedEsModules: true },
  },
}))

function hash() {
  return Math.round(Math.random() * (999 - 1) + 1)
}
