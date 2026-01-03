import { initializeEditor } from './editor';
import { initializeEmbedUploader } from './embed-uploader';

document.addEventListener('DOMContentLoaded', () => {
  initializeEditor();
  initializeEmbedUploader({ root: document });
});
