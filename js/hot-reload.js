export function setupHotReload() {
  if (!window.EventSource) {
    console.warn(
      'EventSource is not supported in this browser. Live reload will not work.',
    );
    return;
  }

  if (!window.ESBUILD_SERVE_HOST) {
    console.warn(
      'ESBUILD_SERVE_HOST is not set in the environment variables. Using default host localhost.',
    );
    window.ESBUILD_SERVE_HOST = 'localhost';
  }

  if (!window.ESBUILD_SERVE_PORT) {
    console.warn(
      'ESBUILD_SERVE_PORT is not set in the environment variables. Using default port 3751.',
    );
    window.ESBUILD_SERVE_PORT = 3751;
  }

  const eventSource = new EventSource(
    `http://${window.ESBUILD_SERVE_HOST}:${window.ESBUILD_SERVE_PORT}/esbuild`,
  );

  eventSource.addEventListener('error', (e) => {
    console.error('Error connecting to esbuild for hot reload:', e);
  });

  eventSource.addEventListener('open', () => {
    eventSource.addEventListener('change', (e) => {
      const { added, removed, updated } = JSON.parse(e.data);

      const updatedJs = updated.filter((file) => file.endsWith('.js'));

      if (added.length || removed.length || updatedJs.length) {
        location.reload();
        return;
      }

      const updatedCss = updated.filter((file) => file.endsWith('.css'));

      for (const cssFile of updatedCss) {
        const links = document.querySelectorAll(
          `link[rel="stylesheet"][href*="${cssFile}"]`,
        );
        links.forEach((link) => {
          link.href = `${link.href.split('?')[0]}?t=${Date.now()}`;
        });

        console.log(`Updated CSS: ${cssFile}`);
      }
    });
  });

  window.addEventListener('beforeunload', () => {
    eventSource.close();
  });
}
