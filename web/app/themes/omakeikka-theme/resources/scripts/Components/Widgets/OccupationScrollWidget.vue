<template>
  <div class="scroll-widget__container">
    <div
      v-for="(occupationTitle, occupationId, index) in occupations"
      :key="occupationId"
      class="scroll-widget scroll-widget--x"
      :style="{
        animationDelay: `${delay * (index - 50 + 1)}ms`,
        top: `${Math.round(index % 5) * 25}px`,
      }"
    >
      <div
        class="scroll-widget--y"
        :style="{
          animationDelay: `${index * -0.2}s`,
          left: `${100 + Math.random() * -200 - occupationTitle.length * 3}px`,
        }"
      >
        <span class="badge bg-dark text-primary rounded-pill">{{ occupationTitle }}</span>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { getOccupations } from '@/api/occupations';

export default defineComponent({
  components: {
  },
  data() {
    return {
      totalDelay: 110,
      occupations: {},
    };
  },
  computed: {
    occupationCount() {
      return Object.entries(this.occupations).length;
    },
    delay() {
      return (this.totalDelay / this.occupationCount) * 1000;
    },
  },
  async mounted() {
    this.occupations = await getOccupations();
  },
});
</script>

<style lang="scss">
$sine-height: 40px;
$anim-width: 14000px;
$random-height: 125px;
$badge-height: 25px;

.scroll-widget {
  &__container {
    min-height: $sine-height + $badge-height + $random-height;
    position: relative;
    overflow-x: hidden;
  }

  &--x {
    position: absolute;
    width: 10%;
    left: -800px;
    overflow-x: visible;
    animation: translate 110s infinite linear;
  }

  &--y {
    position: absolute;
    animation: upDown 2.5s alternate infinite ease-in-out;
    width: 100%;
  }
}

@keyframes upDown {
  to {
    transform: translateY($sine-height);
  }
}

@keyframes translate {
  from {
    transform: translateX(0);
  }

  to {
    transform: translateX($anim-width);
  }
}
</style>
