<template>
  <div class="row">
    <div class="col col-12 col-lg-5">
      <h3 class="text-secondary fs-4 bg-white">
        Valitse kunta ja näe työvoima&shy;tarjonta
      </h3>
      <div class="card shadow-sm municipality-map__info-slot mb-3">
        <div class="card-body">
          <MunicipalityMapForm
            ref="form"
            @select="(municipalityId, subregionId) => selectedMunicipality = {
              municipalityId,
              subregionId,
            }"
          />
          <div v-if="selectedMunicipality">
            <table
              v-if="occupations.length > 0"
              class="table table-striped"
            >
              <thead>
                <tr>
                  <th scope="col">
                    Ammatti
                  </th>
                  <th scope="col">
                    Osuus
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(occupation) in occupations"
                  :key="occupation.id"
                >
                  <td>{{ occupation.title }}</td>
                  <td>{{ pctFormat.format(occupation.fraction) }}</td>
                </tr>
              </tbody>
            </table>
            <a
              href="https://app.omakeikka.fi"
              class="btn btn-primary text-dark w-100"
            >
              Kirjaudu palveluun<i
                aria-hidden="true"
                class="fas fa-long-arrow-alt-right ms-3"
              />
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="col col-12 col-lg-7">
      <MunicipalityMap
        ref="map"
        :selected-municipality="selectedMunicipality"
        @animate="([municipalityId, subregionId, viewbox]) => animate(municipalityId)"
        @select="([municipalityId, subregionId]) => {
          $refs.form.setMunicipality(municipalityId);
        }"
      />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import LeaderLine from 'leader-line-new';
import MunicipalityMap from '@/Components/MunicipalityMap/MunicipalityMap.vue';
import MunicipalityMapForm from '@/Components/MunicipalityMap/MunicipalityMapForm.vue';
import municipalities from '@data/municipalities_full.json';
import AvatarGroup from '@/Components/AvatarGroup.vue';
import { getMunicipalityOccupations, MunicipalityOccupation } from '@/api/municipalities';

export default defineComponent({
  components: {
    AvatarGroup,
    MunicipalityMap,
    MunicipalityMapForm,
  },
  data() {
    return {
      mapEl: null as null | HTMLElement,
      municipalities,
      occupations: [] as MunicipalityOccupation[],
      selectedMunicipality: null as {
        municipalityId: string,
        subregionId: string,
      } | null,
      lines: [] as LeaderLine[],
      pctFormat: new Intl.NumberFormat('fi-FI', {
        style: 'percent',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }),
    };
  },
  watch: {
    selectedMunicipality: {
      async handler(newMunicipality) {
        this.occupations = await getMunicipalityOccupations(newMunicipality.municipalityId);
      },
    },
  },
  mounted() {
    this.mapEl = document.querySelector('.municipality-map');

    this.lines.push(new LeaderLine(
      LeaderLine.pointAnchor(this.mapEl, { x: '50%', y: '50%' }),
      document.querySelector('.municipality-map__info-slot'),
      {
        startPlug: 'disc',
        endPlug: 'disc',
        dropShadow: {
          color: '#4473C5', dx: 0, dy: 0, blur: 5,
        },
        startPlugColor: '#0C254E',
        endPlugColor: '#0C254E',
        gradient: true,
        hide: true,
      },
    ));
  },
  methods: {
    animate(municipalityId: string) {
      const el = document.querySelector(`polygon[municipality-id="${municipalityId}"]`) as SVGElement | null;

      if (!el) {
        return;
      }

      const isInsideBounds = el.getBoundingClientRect().left > this.mapEl?.getBoundingClientRect().left
        && el.getBoundingClientRect().right < this.mapEl?.getBoundingClientRect().right
        && el.getBoundingClientRect().top > this.mapEl?.getBoundingClientRect().top
        && el.getBoundingClientRect().bottom < this.mapEl?.getBoundingClientRect().bottom;

      this.lines.forEach((line: LeaderLine) => {
        if (isInsideBounds) {
          line.show();
          line.start = LeaderLine.pointAnchor(el, { x: '50%', y: '50%' });
          line.position();
        } else {
          line.hide('none');
        }
      });
    },
  },
});
</script>
