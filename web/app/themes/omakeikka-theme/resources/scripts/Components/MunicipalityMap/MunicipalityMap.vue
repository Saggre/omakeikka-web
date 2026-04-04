<template>
  <div
    ref="canvas"
    class="municipality-map__container"
  >
    <slot />
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import map from '@data/map.json';
import { Container as SVGContainer, Element as SVGElement, SVG } from '@svgdotjs/svg.js';
import { animate, easeOut } from 'popmotion';
import { groupBy } from 'underscore';

export default defineComponent({
  props: {
    selectedMunicipality: {
      type: Object as PropType<{
        municipalityId: string,
        subregionId: string,
      } | null>,
      default: null,
    },
  },
  emits: ['animate', 'select'],
  data() {
    return {
      canvas: null as null | (SVGElement & SVGContainer),
      patterns: [],
      oldSvgFill: '' as string,
    };
  },
  watch: {
    selectedMunicipality: {
      handler(newMunicipality, oldMunicipality) {
        if (newMunicipality) {
          this.municipalitySelected(newMunicipality.municipalityId, newMunicipality.subregionId, oldMunicipality?.municipalityId ?? null);
        }
      },
    },
  },
  mounted() {
    this.canvas = SVG().addTo(this.$refs.canvas as HTMLElement)
      .size('100%', '650px')
      .addClass('municipality-map')
      .viewbox(0, 0, 1000, 2100);

    this.patterns = [
      this.canvas.pattern(200, 200, (add) => {
        add.image('/app/themes/omakeikka-theme/resources/images/halftone/Light-Halftone-Screen-Large.png')
          .width(200)
          .height(200);
      }),
      this.canvas.pattern(200, 200, (add) => {
        add.image('/app/themes/omakeikka-theme/resources/images/halftone/Fine-Halftone-Screen-Large.png')
          .width(200)
          .height(200);
      }),
      this.canvas.pattern(150, 150, (add) => {
        add.image('/app/themes/omakeikka-theme/resources/images/halftone/Heavy-Halftone-Screen-Large.png')
          .width(150)
          .height(150);
      }),
      this.canvas.pattern(300, 300, (add) => {
        add.image('/app/themes/omakeikka-theme/resources/images/halftone/Fine-Halftone-Screen-Large.png')
          .width(300)
          .height(300);
      }),
    ];

    const subregionMap = groupBy(map, ({ subregion_id }) => subregion_id);

    Object.entries(subregionMap).forEach(([subregionId, municipalities]) => {
      const group = this.canvas?.group().attr('subregion-id', subregionId);
      this.canvas.add(group);

      municipalities.forEach(({ municipality_id, subregion_id, polygons }, i) => {
        polygons.forEach((polygon) => {
          const _polygon = this.canvas
            ?.polygon(polygon.map(([x, y]) => `${x},${y}`).join(' '))
            .fill('#fff')
            .attr('municipality-id', municipality_id)
            .attr({ fill: this.patterns[i % 3] })
            .click(() => this.$emit('select', [municipality_id, subregion_id]));

          group.add(_polygon);
        });
      });
    });
  },
  methods: {
    municipalitySelected(municipalityId: string, subregionId: string, oldMunicipalityId: string | null = null) {
      const element = this.getSubregionElement(subregionId);
      const bbox = element.getBBox();
      const padding = 70;
      const oldbox = this.canvas.viewbox();

      this.canvas.addClass('municipality-map--selected');

      if (oldMunicipalityId) {
        const oldEl = this.getMunicipalityElement(oldMunicipalityId);
        oldEl?.classList.remove('municipality-map__municipality--selected');
        oldEl?.setAttribute('fill', this.oldSvgFill);
      }

      const newEl = this.getMunicipalityElement(municipalityId);
      newEl?.classList.add('municipality-map__municipality--selected');
      this.oldSvgFill = newEl?.getAttribute('fill') || '';
      newEl?.setAttribute('fill', '#72F48A');

      document.querySelectorAll('.municipality-map g[subregion-id]').forEach((el) => el.setAttribute('visibility', 'hidden'));
      document.querySelector(`.municipality-map g[subregion-id="${subregionId}"]`)?.setAttribute('visibility', 'visible');

      animate({
        elapsed: 0,
        duration: 1000,
        from: `${oldbox.x} ${oldbox.y} ${oldbox.width} ${oldbox.height}`,
        ease: easeOut,
        to: `${bbox.x - padding} ${bbox.y - padding} ${bbox.width + padding * 2} ${bbox.height + padding * 2}`,
        onUpdate: (viewbox) => {
          this.canvas.viewbox(viewbox);
          this.$emit('animate', [municipalityId, subregionId, viewbox]);
        },
      });
    },
    getSubregionElement(subregionId: string): SVGSVGElement {
      // Returns a <g> element.
      return document.querySelector(`g[subregion-id="${subregionId}"]`) as unknown as SVGSVGElement;
    },
    getMunicipalityElement(municipalityId: string): SVGSVGElement {
      // Returns a <polygon> element.
      return document.querySelector(`polygon[municipality-id="${municipalityId}"]`) as unknown as SVGSVGElement;
    },
  },
});
</script>
