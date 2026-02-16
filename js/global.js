import ajax from '@imacrayon/alpine-ajax';
import Alpine from 'alpinejs';
import { setupHotReload } from './hot-reload';

window.Alpine = Alpine;
Alpine.plugin(ajax);
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
  if (window.APP_ENV === 'development') {
    console.log('Running in development mode');
    setupHotReload();
  }
});
