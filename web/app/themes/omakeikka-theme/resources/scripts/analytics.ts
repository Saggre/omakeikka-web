type EventParams = Record<string, string | number | undefined>;

declare global {
  interface Window {
    gtag?: (...args: unknown[]) => void;
  }
}

/**
 * GA4 events, sent only after statistics consent.
 *
 * Complianz keeps gtag.js and its config as blocked scripts until the visitor
 * consents to statistics, and window.gtag does not exist before that. Events
 * are held here and sent once Complianz has run the GA config, so nothing
 * reaches Google before consent. Parameters never carry personal data.
 */
const queue: Array<[string, EventParams]> = [];

const flush = () => {
  if (window.gtag) {
    queue.splice(0).forEach(([name, params]) => window.gtag?.('event', name, params));
  }
};

const track = (name: string, params: EventParams = {}) => {
  queue.push([name, params]);
  flush();
};

document.addEventListener('cmplz_enable_category', ((event: CustomEvent<{ category: string }>) => {
  if (event.detail?.category === 'statistics') {
    flush();
  }
}) as EventListener);

// Delegated from document, since Vue replaces the in-DOM elements when it mounts.
document.addEventListener('click', (event) => {
  const link = (event.target as Element).closest<HTMLElement>('[data-cta]');

  if (link) {
    track('cta_click', {
      cta: link.dataset.cta,
      group: link.dataset.category,
      page_path: window.location.pathname,
    });
  }
});

const startedForms = new WeakSet<HTMLFormElement>();

document.addEventListener('focusin', (event) => {
  const form = (event.target as Element).closest<HTMLFormElement>('form[data-analytics-form]');

  if (form && !startedForms.has(form)) {
    startedForms.add(form);
    track('form_start', { form: form.dataset.analyticsForm });
  }
});

/**
 * Server rendered events, e.g. generate_lead on the help request thank you
 * page. The page renders the element only on the first view after a submit.
 */
export const trackRenderedEvents = () => {
  document.querySelectorAll<HTMLElement>('[data-analytics-event]').forEach((element) => {
    const { name, params } = JSON.parse(element.dataset.analyticsEvent ?? '{}');

    if (name) {
      track(name, params);
    }
  });
};
