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
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
// @ts-ignore
if (import.meta.webpackHot) {
  // @ts-ignore
  import.meta.webpackHot.accept(console.error);
}
