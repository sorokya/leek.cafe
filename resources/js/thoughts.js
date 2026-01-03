import { initializeEmbedUploader } from './embed-uploader';

document.addEventListener('DOMContentLoaded', () => {
  const composer = document.querySelector('.thoughts-composer');
  if (!composer) return;

  const textarea = composer.querySelector('textarea[name="body"]');
  if (textarea) {
    textarea.setAttribute('data-embed-paste-target', 'true');
  }

  initializeEmbedUploader({ root: composer });
});
