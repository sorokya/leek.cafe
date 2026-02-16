import * as esbuild from 'esbuild';
import dotenv from 'dotenv';

dotenv.config({quiet: true});

const ctx = await esbuild.context({
  entryPoints: ['css/global.css', 'css/login.css', 'js/global.js'],
  bundle: true,
  outdir: 'public',
  entryNames: '[ext]/[name]',
  assetNames: 'assets/[name]',
  minify: true,
  sourcemap: true,
  target: ['es2020'],
  loader: {
    '.png': 'file',
    '.jpg': 'file',
    '.jpeg': 'file',
    '.gif': 'file',
  },
});

if (process.argv.includes('--watch')) {
  await ctx.watch();
  console.log('Watching for changes...');

  if (process.argv.includes('--serve')) {
    if (!process.env.ESBUILD_SERVE_PORT) {
      console.warn('ESBUILD_SERVE_PORT is not set in the environment variables. Using default port 3751.');
    }

    if (!process.env.ESBUILD_SERVE_HOST) {
      console.warn('ESBUILD_SERVE_HOST is not set in the environment variables. Using default host localhost.');
    }

    const { port } = await ctx.serve({
      servedir: 'public',
      host: process.env.ESBUILD_SERVE_HOST || 'localhost',
      port: process.env.ESBUILD_SERVE_PORT ? parseInt(process.env.ESBUILD_SERVE_PORT, 10) : 3751,
    });
    console.log(`Serving on http://${process.env.ESBUILD_SERVE_HOST || 'localhost'}:${port}`);
  }
} else {
  await ctx.rebuild();
  await ctx.dispose();
}