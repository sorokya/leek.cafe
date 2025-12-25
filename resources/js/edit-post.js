import { initializeEditor } from './editor';

document.addEventListener('DOMContentLoaded', () => {
  initializeEditor();

  const btnUnpublish = document.querySelector('#btn-unpublish');
  const publishDate = document.querySelector('#published_at');
  if (btnUnpublish) {
    btnUnpublish.addEventListener('click', () => {
      publishDate.value = '';
    });
  }
});
