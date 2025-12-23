export default function initMobileNav() {
  const toggleButton = document.querySelector('[data-nav-toggle]');
  const mobileNavId = toggleButton?.getAttribute('aria-controls');
  const mobileNav = mobileNavId ? document.getElementById(mobileNavId) : null;

  if (toggleButton && mobileNav) {
    const setExpanded = (expanded) => {
      toggleButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');

      if (expanded) {
        mobileNav.removeAttribute('hidden');
      } else {
        mobileNav.setAttribute('hidden', '');
      }
    };

    setExpanded(false);

    toggleButton.addEventListener('click', () => {
      const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
      setExpanded(!isExpanded);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape') return;
      setExpanded(false);
    });

    document.addEventListener('click', (event) => {
      const isExpanded = toggleButton.getAttribute('aria-expanded') === 'true';
      if (!isExpanded) return;

      const target = event.target;
      if (!(target instanceof Node)) return;

      if (toggleButton.contains(target)) return;
      if (mobileNav.contains(target)) return;

      setExpanded(false);
    });
  }
}
