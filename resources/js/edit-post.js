import OverType from 'overtype';

document.addEventListener('DOMContentLoaded', () => {
  const theme = document.body.getAttribute('data-theme') || 'light';
  const hiddenInput = document.querySelector('#body');
  const [editor] = new OverType('#body-editor', {
    value: hiddenInput.value,
    toolbar: true,
    showStats: true,
    theme: theme === 'dark' ? 'cave' : 'solar',
  });

  editor.textarea.addEventListener('change', () => {
    hiddenInput.value = editor.getValue();
  });

  const btnUnpublish = document.querySelector('#btn-unpublish');
  const publishDate = document.querySelector('#published_at');
  if (btnUnpublish) {
    btnUnpublish.addEventListener('click', () => {
      publishDate.value = '';
    });
  }
});
