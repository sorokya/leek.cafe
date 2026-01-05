import { initializeEmbedUploader } from './embed-uploader';

function shouldLetBrowserHandleClick(event) {
  return (
    event.defaultPrevented ||
    event.button !== 0 ||
    event.metaKey ||
    event.ctrlKey ||
    event.shiftKey ||
    event.altKey
  );
}

function collapseEmbedItem(item) {
  if (!item?.classList?.contains('embed-item--expanded')) return;

  const originalHtml = item.dataset.embedOriginalHtml;
  if (originalHtml) {
    item.classList.remove('embed-item--expanded');
    item.innerHTML = originalHtml;
    delete item.dataset.embedOriginalHtml;
  }
}

function collapseAllExpandedEmbeds(exceptItem = null) {
  for (const item of document.querySelectorAll(
    '.embed-item.embed-item--expanded',
  )) {
    if (exceptItem && item === exceptItem) continue;
    collapseEmbedItem(item);
  }
}

function expandEmbed(anchor) {
  const item = anchor.closest('.embed-item');
  if (!item) return;
  if (item.classList.contains('embed-item--expanded')) return;

  const kind = anchor.dataset.embedKind;
  if (kind !== 'image' && kind !== 'video') return;

  collapseAllExpandedEmbeds(item);

  const originalHtml = item.innerHTML;
  item.dataset.embedOriginalHtml = originalHtml;
  item.classList.add('embed-item--expanded');

  const src = anchor.href;
  let media;
  if (kind === 'video') {
    media = document.createElement('video');
    media.src = src;
    media.controls = true;
    media.playsInline = true;
    media.preload = 'metadata';
  } else {
    media = document.createElement('img');
    media.src = src;
    media.alt = '';
    media.loading = 'lazy';
    media.decoding = 'async';
    media.addEventListener('click', () => {
      collapseEmbedItem(item);
    });
  }
  media.className = 'embed-media';

  const wrapper = document.createElement('div');
  wrapper.className = 'embed-expanded';

  if (kind === 'video') {
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'embed-collapse';
    closeButton.textContent = 'Close';
    closeButton.addEventListener('click', () => {
      collapseEmbedItem(item);
    });
    wrapper.append(closeButton);
  }

  wrapper.append(media);

  item.replaceChildren(wrapper);

  requestAnimationFrame(() => {
    wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  document.addEventListener('click', (event) => {
    const anchor = event.target.closest('a.embed-thumb[data-embed-kind]');
    if (!anchor) return;
    if (shouldLetBrowserHandleClick(event)) return;

    event.preventDefault();
    expandEmbed(anchor);
  });

  const composer = document.querySelector('.thoughts-composer');
  if (composer) {
    const textarea = composer.querySelector('textarea[name="body"]');
    if (textarea) {
      textarea.setAttribute('data-embed-paste-target', 'true');
    }

    initializeEmbedUploader({ root: composer });
  }

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
