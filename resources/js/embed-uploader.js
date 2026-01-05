function parseEmbeds(value) {
  if (!value) return [];
  return Array.from(
    new Set(
      value
        .split(',')
        .map((h) => h.trim())
        .filter(Boolean),
    ),
  );
}

function writeEmbeds(hiddenInput, hashes) {
  hiddenInput.value = hashes.join(',');
}

function renderEmbeds(listEl, hashes) {
  if (!listEl) return;

  listEl.innerHTML = '';

  if (hashes.length === 0) {
    return;
  }

  for (const hash of hashes) {
    const item = document.createElement('div');
    item.className = 'embed-item';

    const link = document.createElement('a');
    link.className = 'embed-thumb';
    link.href = `/img/${hash}`;
    link.target = '_blank';
    link.rel = 'noopener';

    const img = document.createElement('img');
    img.src = `/img/${hash}/thumbnail`;
    img.alt = '';
    img.loading = 'lazy';
    img.decoding = 'async';

    link.appendChild(img);

    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'embed-remove';
    remove.dataset.embedRemove = hash;
    remove.setAttribute('aria-label', 'Remove image');
    remove.textContent = '×';

    item.appendChild(link);
    item.appendChild(remove);

    listEl.appendChild(item);
  }
}

async function uploadImage({ file, contentType, csrfToken }) {
  if (!file?.type?.startsWith('image/') && !file?.type?.startsWith('video/')) {
    return null;
  }

  const formData = new FormData();
  formData.append('_token', csrfToken);
  formData.append('image[]', file);

  const response = await fetch(`/${contentType}/upload-images`, {
    method: 'POST',
    body: formData,
    headers: {
      Accept: 'application/json',
    },
  });

  if (!response.ok) {
    console.error('Image upload failed', response);
    return null;
  }

  const { hashes } = await response.json();
  const first = Array.isArray(hashes) ? hashes[0] : null;
  return typeof first === 'string' && first.length > 0 ? first : null;
}

export function initializeEmbedUploader(options = {}) {
  const root = options.root || document;

  const hiddenInput = root.querySelector('input[name="embeds"]');
  const listEl = root.querySelector('[data-embed-list]');
  const fileInput = root.querySelector('[data-embed-input]');

  if (!hiddenInput || !listEl) {
    return;
  }

  const contentType = options.contentType || location.pathname.split('/')[1];

  const csrfToken = document.querySelector('[name="_token"]')?.value;
  if (!csrfToken) {
    return;
  }

  let hashes = parseEmbeds(hiddenInput.value);
  renderEmbeds(listEl, hashes);

  function setHashes(next) {
    hashes = next;
    writeEmbeds(hiddenInput, hashes);
    renderEmbeds(listEl, hashes);
  }

  listEl.addEventListener('click', (e) => {
    const button = e.target.closest('[data-embed-remove]');
    if (!button) return;

    const hash = button.dataset.embedRemove;
    setHashes(hashes.filter((h) => h !== hash));
  });

  async function handleFiles(fileList) {
    if (!fileList || fileList.length === 0) return;

    const files = Array.from(fileList).filter(
      (f) => f?.type?.startsWith('image/') || f?.type?.startsWith('video/'),
    );
    if (files.length === 0) return;

    if (fileInput) {
      fileInput.value = '';
    }

    for (const file of files) {
      uploadImage({ file, contentType, csrfToken }).then((hash) => {
        if (!hash) return;

        setHashes(Array.from(new Set([...hashes, hash])));
      });
    }
  }

  if (fileInput) {
    fileInput.addEventListener('change', async (e) => {
      await handleFiles(e.target.files);
    });
  }

  const pasteTarget =
    options.pasteTarget || root.querySelector('[data-embed-paste-target]');
  if (pasteTarget) {
    pasteTarget.addEventListener('paste', async (e) => {
      const files = e?.clipboardData?.files;
      if (files?.length) {
        e.preventDefault();
        await handleFiles(files);
      }
    });

    pasteTarget.addEventListener('dragover', (e) => {
      e.preventDefault();
    });

    pasteTarget.addEventListener('drop', async (e) => {
      e.preventDefault();
      await handleFiles(e.dataTransfer?.files);
    });
  }
}
