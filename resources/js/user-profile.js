const containerSelector = '[data-day-view]';

async function loadDay(url) {
  const container = document.querySelector(containerSelector);
  if (!container) return;

  container.setAttribute('aria-busy', 'true');

  try {
    const response = await fetch(url, {
      headers: {
        'X-Requested-With': 'fetch',
      },
    });

    if (!response.ok) {
      throw new Error(`Failed to load day view (${response.status})`);
    }

    const html = await response.text();
    container.innerHTML = html;

    // Re-bind after swap
    bindDayView(container);
  } finally {
    container.removeAttribute('aria-busy');
  }
}

function bindDayView(root) {
  const prev = root.querySelector('[data-day-prev]');
  const next = root.querySelector('[data-day-next]');
  const picker = root.querySelector('[data-day-picker]');
  const calendarButton = root.querySelector('[data-day-calendar]');
  const dayLink = root.querySelector('[data-day-link]');
  const saveForm = root.querySelector('[data-day-save-form]');

  const fragmentUrlForDate = (date) => {
    const base = dayLink?.getAttribute('href');
    if (!base) return null;

    // base is /user/<username>/YYYY-MM-DD
    return `${`${base}`.replace(/\d{4}-\d{2}-\d{2}$/, date)}/day`;
  };

  const pageUrlForDate = (date) => {
    const base = dayLink?.getAttribute('href');
    if (!base) return null;

    return `${base}`.replace(/\d{4}-\d{2}-\d{2}$/, date);
  };

  const onNavClick = async (event, element) => {
    const href = element.getAttribute('href');
    if (!href) return;

    event.preventDefault();
    await loadDay(href + '/day');

    // Update URL to the non-fragment page route
    history.pushState({}, '', href);
  };

  if (prev) {
    prev.addEventListener('click', (e) => onNavClick(e, prev));
  }

  if (next) {
    next.addEventListener('click', (e) => onNavClick(e, next));
  }

  if (calendarButton && picker) {
    calendarButton.addEventListener('click', () => {
      if (typeof picker.showPicker === 'function') {
        picker.showPicker();
      } else {
        picker.focus();
        picker.click();
      }
    });
  }

  if (picker) {
    picker.addEventListener('change', async () => {
      const date = picker.value;
      if (!date) return;

      const fragmentUrl = fragmentUrlForDate(date);
      const pageUrl = pageUrlForDate(date);
      if (!fragmentUrl || !pageUrl) return;

      await loadDay(fragmentUrl);
      history.pushState({}, '', pageUrl);
    });
  }

  if (saveForm) {
    saveForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      try {
        const response = await fetch(saveForm.action, {
          method: 'POST',
          body: new FormData(saveForm),
          headers: {
            'X-Requested-With': 'fetch',
            Accept: 'application/json',
          },
        });

        if (!response.ok) {
          // Fall back to a regular form post so validation errors render.
          saveForm.submit();
          return;
        }

        await loadDay(window.location.pathname + '/day');
      } catch {
        saveForm.submit();
      }
    });
  }

  window.addEventListener('popstate', () => {
    // Best effort: on back/forward, reload to match URL.
    loadDay(window.location.pathname + '/day');
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector(containerSelector);
  if (!container) return;

  bindDayView(container);
});
