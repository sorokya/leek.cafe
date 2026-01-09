import OverType from 'overtype';

export function initializeEditor() {
  const theme = document.body.getAttribute('data-theme') || 'light';
  const hiddenInput = document.querySelector('#body');
  const [editor] = new OverType('#body-editor', {
    value: hiddenInput.value,
    toolbar: true,
    showStats: true,
    theme: theme === 'dark' ? 'cave' : 'solar',
  });

  editor.textarea.addEventListener('input', () => {
    hiddenInput.value = editor.getValue();
  });

  editor.textarea.addEventListener('paste', async (e) => {
    if (e?.clipboardData?.files?.length) {
      e.preventDefault();
    }
    await handleDataTransfer(e.clipboardData);
  });

  editor.textarea.addEventListener('dragover', (e) => {
    e.preventDefault();
  });

  editor.textarea.addEventListener('drop', async (e) => {
    e.preventDefault();
    await handleDataTransfer(e.dataTransfer);
  });

  async function handleDataTransfer(dataTransfer) {
    const files = dataTransfer?.files;
    if (!files) return;

    const csrfToken = document.querySelector('[name="_token"]').value;
    if (!csrfToken) return;

    const formData = new FormData();
    const placeholders = [];
    formData.append('_token', csrfToken);
    let index = 0;
    for (const file of files) {
      if (file.type.startsWith('image/') || file.type.startsWith('video/')) {
        if (!file) continue;

        const placeholder = `![](@img:uploading-${Date.now()}-${index})`;
        placeholders.push(placeholder);
        insertAtCursor(`${placeholder}\n`);

        formData.append('image[]', file);
        index++;
      }
    }

    if (formData.getAll('image[]').length === 0) {
      return;
    }

    try {
      const response = await fetch(`/upload-images`, {
        method: 'POST',
        body: formData,
        headers: {
          Accept: 'application/json',
        },
      });

      if (!response.ok) {
        const content = editor.getValue();

        // Best-effort parse of Laravel validation errors (422).
        let errorByIndex = [];
        try {
          const payload = await response.json();
          const errors = payload?.errors;

          if (errors && typeof errors === 'object') {
            errorByIndex = placeholders.map((_, i) => {
              const key = `image.${i}`;
              const value = errors[key] || errors.image;
              const first = Array.isArray(value) ? value[0] : null;
              return typeof first === 'string' && first.length > 0
                ? first
                : null;
            });
          }
        } catch {
          // Ignore JSON parsing errors; fall back to a generic message.
        }

        const fallbackMessage =
          response.status === 413
            ? 'Upload is too large.'
            : `Upload failed (HTTP ${response.status}).`;

        const updatedContent = placeholders.reduce((text, placeholder, i) => {
          const message = errorByIndex[i] || fallbackMessage;
          return text.replace(placeholder, message);
        }, content);

        editor.setValue(updatedContent);
        hiddenInput.value = editor.getValue();
        return;
      }

      const { hashes } = await response.json();

      hashes.forEach((hash) => {
        const markdownImage = `![](@img:${hash})`;
        const placeholder = placeholders.shift();
        const content = editor.getValue();
        const updatedContent = content.replace(placeholder, markdownImage);
        editor.setValue(updatedContent);
        hiddenInput.value = editor.getValue();
      });
    } catch (error) {
      console.error('Error uploading image:', error);

      const content = editor.getValue();
      const updatedContent = placeholders.reduce((text, placeholder) => {
        return text.replace(placeholder, 'Upload failed.');
      }, content);
      editor.setValue(updatedContent);
      hiddenInput.value = editor.getValue();
    }
  }

  function insertAtCursor(text) {
    const start = editor.textarea.selectionStart;
    const end = editor.textarea.selectionEnd;

    // Try native method first (preserves undo history)
    if (!document.execCommand('insertText', false, text)) {
      // Fallback to direct manipulation
      const before = editor.textarea.value.slice(0, start);
      const after = editor.textarea.value.slice(end);
      editor.textarea.value = before + text + after;
      editor.textarea.setSelectionRange(
        start + text.length,
        start + text.length,
      );
    }

    // Trigger input event to update preview
    editor.textarea.dispatchEvent(new Event('input', { bubbles: true }));
  }
}
