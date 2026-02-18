export function setupAutoplay() {
  const elements = document.querySelectorAll('audio[autoplay]');
  if (elements.length === 0) return;

  for (const audio of elements) {
    audio.volume = 0.2;
  }

  document.addEventListener('toggle-music', () => {
    for (const audio of elements) {
      if (audio.paused) {
        audio.play().catch((err) => {
          console.warn('Failed to play audio:', err);
        });
      } else {
        audio.pause();
      }
    }
  });
}
