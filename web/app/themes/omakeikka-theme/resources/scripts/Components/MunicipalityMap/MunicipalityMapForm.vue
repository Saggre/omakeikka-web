<template>
  <form>
    <div class="mb-3">
      <label
        for="municipality"
        class="form-label"
      >Kunta</label>
      <select
        id="municipality"
        ref="municipality"
        class="form-select  form-select-lg"
        @change="(event) => setMunicipality(event.target.value)"
      >
        <option selected>
          {{ 'Valitse kunta' }}
        </option>
        <option
          v-for="(municipality) in Object.values(municipalities).sort((a, b) => a.title.fi.localeCompare(b.title.fi, 'fi-FI'))"
          :key="municipality.municipality_id"
          :value="municipality.municipality_id"
        >
          {{ municipality.title.fi }}
        </option>
      </select>
    </div>
  </form>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import municipalities from '@data/municipalities_full.json';
import { Municipalities } from '@/data';

export default defineComponent({
  emits: ['select'],
  data(): {
    municipalities: Municipalities;
    } {
    return {
      municipalities,
    };
  },
  methods: {
    setMunicipality(municipalityId: string) {
      this.$emit('select', municipalityId, this.municipalities[municipalityId].subregion_id);
      (this.$refs.municipality as HTMLSelectElement).value = municipalityId;
    },
  },
});
</script>
