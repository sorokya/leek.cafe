import { initializeEmbedUploader } from './embed-uploader';

document.addEventListener('DOMContentLoaded', () => {
  const composer = document.querySelector('.thoughts-composer');
  if (!composer) return;

  const textarea = composer.querySelector('textarea[name="body"]');
  if (textarea) {
    textarea.setAttribute('data-embed-paste-target', 'true');
  }

  initializeEmbedUploader({ root: composer });

  for (const item of document.querySelectorAll('[data-thought-item]')) {
    const actions = item.querySelector('[data-thought-actions]');
    const editLink = item.querySelector('[data-thought-edit-link]');
    const deleteLink = item.querySelector('[data-thought-delete]');
    const view = item.querySelector('[data-thought-view]');
    const edit = item.querySelector('[data-thought-edit-panel]');
    const cancel = item.querySelector('[data-thought-cancel]');
    const deleteForm = item.querySelector('[data-thought-delete-form]');

    if (edit && !edit.hasAttribute('data-embed-uploader-initialized')) {
      initializeEmbedUploader({ root: edit });
      edit.setAttribute('data-embed-uploader-initialized', 'true');
    }

    if (editLink && view && edit) {
      editLink.addEventListener('click', (e) => {
        e.preventDefault();
        if (actions) actions.removeAttribute('open');

        view.hidden = true;
        edit.hidden = false;

        const textarea = edit.querySelector('textarea[name="body"]');
        if (textarea) {
          textarea.focus();
          textarea.selectionStart = textarea.value.length;
          textarea.selectionEnd = textarea.value.length;
        }
      });
    }

    if (cancel && view && edit) {
      cancel.addEventListener('click', () => {
        view.hidden = false;
        edit.hidden = true;
      });
    }

    if (deleteLink && deleteForm) {
      deleteLink.addEventListener('click', (e) => {
        e.preventDefault();
        if (actions) actions.removeAttribute('open');

        if (confirm('Delete this thought?')) {
          deleteForm.submit();
        }
      });
    }
  }
});
