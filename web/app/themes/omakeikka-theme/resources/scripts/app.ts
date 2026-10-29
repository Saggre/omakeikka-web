import domReady from '@roots/sage/client/dom-ready';
import 'bootstrap';
import { createApp } from 'vue';
import Badge from '@/Components/Badge.vue';
import MunicipalityMapWidget from '@/Components/Widgets/MunicipalityMapWidget.vue';
import OccupationScrollWidget from '@/Components/Widgets/OccupationScrollWidget.vue';

/**
 * Application entrypoint
 */
domReady(async () => {
  // The ALTCHA widget is loaded only on pages with a protected form. The
  // translations register into the global the main bundle defines.
  if (document.querySelector('altcha-widget')) {
    import('altcha').then(() => import('altcha/i18n/fi')).catch(() => {});
  }

  const app = createApp({});

  app.component('Badge', Badge);
  app.component('MunicipalityMapWidget', MunicipalityMapWidget);
  app.component('OccupationScrollWidget', OccupationScrollWidget);
  app.mount('#app');
});

type AltchaWidget = HTMLElement & { getState(): string; verify(): Promise<unknown> };

// Forms whose next submit goes through as is, after the widget settled.
const releasedForms = new WeakSet<HTMLFormElement>();

/**
 * Stops a double click from posting the same help request twice. Delegated
 * from document, since Vue replaces the in-DOM elements when it mounts.
 *
 * The invisible ALTCHA widget solves in the background from the first focus.
 * A submit that beats it waits for the solve and then submits again. An error
 * submits too, so the server shows its message instead of the button hanging.
 */
document.addEventListener('submit', (event) => {
  const form = event.target as HTMLFormElement;

  if (!form.matches('.help-request__form')) {
    return;
  }

  const widget = form.querySelector('altcha-widget') as AltchaWidget | null;

  // An undefined element means the lazy chunk has not loaded, so the server reports the missing payload.
  if (releasedForms.has(form)) {
    releasedForms.delete(form);
  } else if (widget && customElements.get('altcha-widget') && widget.getState() !== 'verified') {
    event.preventDefault();

    const onStateChange = ({ detail }: CustomEvent<{ state: string }>) => {
      if (['verified', 'error'].includes(detail.state)) {
        widget.removeEventListener('statechange', onStateChange as EventListener);
        releasedForms.add(form);
        form.requestSubmit();
      }
    };

    widget.addEventListener('statechange', onStateChange as EventListener);

    if (widget.getState() !== 'verifying') {
      widget.verify();
    }
  }

  form.querySelector('button[type="submit"]')?.setAttribute('disabled', '');
});

/**
 * Swaps the help request starting text when the group changes. Text the visitor
 * has written is kept: only empty fields, unedited server prefill and another
 * group's starting text are replaced.
 */
document.addEventListener('change', (event) => {
  const select = event.target as HTMLSelectElement;

  if (!select.matches('[data-prefill]') || !select.form) {
    return;
  }

  const prefills: Record<string, Record<string, string>> = JSON.parse(select.dataset.prefill ?? '{}');
  const { elements } = select.form;

  ['title', 'description'].forEach((field) => {
    const input = elements.namedItem(field) as HTMLInputElement | HTMLTextAreaElement;
    const untouched = input.value === ''
      || ('prefilled' in input.dataset && input.value === input.defaultValue)
      || Object.values(prefills).some((texts) => texts[field] === input.value);

    if (untouched) {
      input.value = prefills[select.value]?.[field] ?? '';
    }
  });

  // A category from the link no longer applies once the group changes.
  (elements.namedItem('category_slug') as HTMLInputElement).value = '';
});

// The back button can restore the page from bfcache with the button still disabled.
window.addEventListener('pageshow', () => {
  document.querySelectorAll('.help-request__form button[disabled]').forEach((button) => button.removeAttribute('disabled'));
});

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
// @ts-ignore
if (import.meta.webpackHot) {
  // @ts-ignore
  import.meta.webpackHot.accept(console.error);
}
