<template>
  <div class="d-flex gap-1">
    <template v-for="(link, index) in links">
      <Link v-if="link.url !== undefined && link.url !== null" :key="index"
        :class="[link.active ? 'btn btn-primary fw-semibold' : 'btn btn-outline-primary border-2 fw-semibold']"
        :href="link.url" v-html="getLabel(link)">
      </Link>
    </template>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps(['links']);

const getLabel = (link) => {
  if (link.label.includes('Next')) {
    return '>';
  } else if (link.label.includes('Previous')) {
    return '<';
  } else {
    return link.label;
  }
};

</script>

<style scoped>
.btn-primary {
  background-color: #007bff;
  color: #fff;
}
</style>
