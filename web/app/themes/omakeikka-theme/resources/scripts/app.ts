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
  const app = createApp({});

  app.component('Badge', Badge);
  app.component('MunicipalityMapWidget', MunicipalityMapWidget);
  app.component('OccupationScrollWidget', OccupationScrollWidget);
  app.mount('#app');
});

/**
 * Stops a double click from posting the same help request twice. Delegated
 * from document, since Vue replaces the in-DOM elements when it mounts.
 */
document.addEventListener('submit', (event) => {
  const form = event.target as HTMLFormElement;

  if (form.matches('.help-request__form')) {
    form.querySelector('button[type="submit"]')?.setAttribute('disabled', '');
  }
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
