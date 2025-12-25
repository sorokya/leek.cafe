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

  editor.textarea.addEventListener('change', () => {
    hiddenInput.value = editor.getValue();
  });

  editor.textarea.addEventListener('paste', async (e) => {
    const items = e.clipboardData?.items;
    if (!items) return;

    const formData = new FormData();
    for (const item of items) {
      if (item.type.startsWith('image/')) {
        e.preventDefault();
        const file = item.getAsFile();
        if (!file) continue;

        formData.append('image', file);
      }
    }

    try {
      const response = await fetch('./upload-image', {
        method: 'POST',
        body: formData,
      });

      if (!response.ok) {
        console.error('Image upload failed', response);
        return;
      }

      const data = await response.json();
    } catch (error) {
      console.error('Error uploading image:', error);
    }
  });
}
