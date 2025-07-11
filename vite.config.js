import { defineConfig } from 'vite';
import dotenv from 'dotenv';
import bootstrap from '@popperjs/core';

dotenv.config();

export default defineConfig({
  publicDir: 'assets/static',
  server: {
    cors: true,
    strictPort: true,
  },
  build: {
    assetsDir: '',
    emptyOutDir: true,
    manifest: true,
    outDir: `public/app/themes/${process.env.MWP_THEME}/assets`,
    rollupOptions: {
      input: 'assets/js/index.js',
    },
  },
  plugins: [
    {
      name: 'php',
      handleHotUpdate({ file, server }) {
        if (file.endsWith('.php')) {
          server.ws.send({ type: 'full-reload', path: '*' });
        }
      },
    },
  ],
});
