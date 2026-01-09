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

  const originalViewHtml = new WeakMap();

  async function fetchHtml(url) {
    const response = await fetch(url, {
      headers: {
        'X-Requested-With': 'fetch',
      },
    });

    if (!response.ok) {
      throw new Error(`Fetch failed (${response.status})`);
    }

    return await response.text();
  }

  function initializeEmbedUploadersIn(root) {
    if (!root) return;

    for (const form of root.querySelectorAll('form[data-thought-edit-form]')) {
      if (form.hasAttribute('data-embed-uploader-initialized')) continue;
      initializeEmbedUploader({ root: form });
      form.setAttribute('data-embed-uploader-initialized', 'true');
    }
  }

  function getThoughtItem(element) {
    return element?.closest?.('[data-thought-item]') ?? null;
  }

  function closeActionsMenu(item) {
    const actions = item?.querySelector?.('[data-thought-actions]');
    if (actions) actions.removeAttribute('open');
  }

  async function enterEditMode(item) {
    const view = item.querySelector('[data-thought-view]');
    const edit = item.querySelector('[data-thought-edit]');
    if (!view || !edit) return;

    if (!originalViewHtml.has(item)) {
      originalViewHtml.set(item, view.innerHTML);
    }

    const editUrl = item.dataset.thoughtEditFragmentUrl;
    if (!editUrl) return;

    item.setAttribute('aria-busy', 'true');
    try {
      const html = await fetchHtml(editUrl);
      edit.innerHTML = html;
      edit.hidden = false;
      view.hidden = true;

      initializeEmbedUploadersIn(edit);

      const textarea = edit.querySelector('textarea[name="body"]');
      if (textarea) {
        textarea.focus();
        textarea.selectionStart = textarea.value.length;
        textarea.selectionEnd = textarea.value.length;
      }
    } finally {
      item.removeAttribute('aria-busy');
    }
  }

  function exitEditMode(item, { restoreOriginalView = true } = {}) {
    const view = item.querySelector('[data-thought-view]');
    const edit = item.querySelector('[data-thought-edit]');
    if (!view || !edit) return;

    if (restoreOriginalView) {
      const cached = originalViewHtml.get(item);
      if (typeof cached === 'string') {
        view.innerHTML = cached;
      }
    }

    edit.innerHTML = '';
    edit.hidden = true;
    view.hidden = false;
    originalViewHtml.delete(item);
  }

  async function refreshThoughtItem(item) {
    const viewUrl = item.dataset.thoughtViewFragmentUrl;
    if (!viewUrl) return;

    const html = await fetchHtml(viewUrl);

    const template = document.createElement('template');
    template.innerHTML = html.trim();

    const next = template.content.firstElementChild;
    if (!next || !next.matches('[data-thought-item]')) {
      throw new Error('Invalid thought fragment response');
    }

    item.replaceWith(next);
    return next;
  }

  document.addEventListener('click', (event) => {
    const editLink = event.target.closest('[data-thought-edit-link]');
    if (editLink) {
      if (shouldLetBrowserHandleClick(event)) return;
      event.preventDefault();

      const item = getThoughtItem(editLink);
      if (!item) return;

      closeActionsMenu(item);
      enterEditMode(item);
      return;
    }

    const cancelButton = event.target.closest('[data-thought-cancel]');
    if (cancelButton) {
      const item = getThoughtItem(cancelButton);
      if (!item) return;

      exitEditMode(item, { restoreOriginalView: true });
      return;
    }

    const deleteLink = event.target.closest('[data-thought-delete]');
    if (deleteLink) {
      if (shouldLetBrowserHandleClick(event)) return;
      event.preventDefault();

      const item = getThoughtItem(deleteLink);
      if (!item) return;

      closeActionsMenu(item);

      const deleteForm = item.querySelector('form[data-thought-delete-form]');
      if (!deleteForm) return;

      if (confirm('Delete this thought?')) {
        deleteForm.submit();
      }
    }
  });

  document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!form.matches('form[data-thought-edit-form]')) return;

    event.preventDefault();

    const item = getThoughtItem(form);
    if (!item) {
      form.submit();
      return;
    }

    item.setAttribute('aria-busy', 'true');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
          'X-Requested-With': 'fetch',
          Accept: 'application/json',
        },
      });

      if (!response.ok) {
        // Fall back so validation errors render normally.
        form.submit();
        return;
      }

      originalViewHtml.delete(item);
      const nextItem = await refreshThoughtItem(item);
      if (nextItem) {
        nextItem.removeAttribute('aria-busy');
      }
    } catch {
      form.submit();
    } finally {
      item.removeAttribute('aria-busy');
    }
  });
});
