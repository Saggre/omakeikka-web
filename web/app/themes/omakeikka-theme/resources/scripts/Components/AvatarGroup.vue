<template>
  <div class="avatar-group d-flex">
    <div
      v-for="(avatar, index) in avatars"
      :key="index"
      class="avatar-group__avatar"
    >
      <img
        :alt="`Avatar ${index}`"
        :src="`/app/themes/omakeikka-theme/resources/images/avatars/${avatar}.jpg`"
      >
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  data() {
    return {
      avatars: ['10', '43', '129', '168', '183', '313', '345', '355'].sort(() => 0.5 - Math.random()),
    };
  },
});
</script>
