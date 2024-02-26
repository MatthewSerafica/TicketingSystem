<template>
    <div class="d-flex gap-1">
      <template v-for="(link, index) in links">
        <Link v-if="link.url !== undefined && link.url !== null" :key="index"
          class="d-flex align-items-center justify-content-center text-white bg-primary rounded"
          :href="link.url" :class="{ 'bg-primary text-secondary': link.active }" v-html="getLabel(link)">
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