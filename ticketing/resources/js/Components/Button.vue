<template>
    <button :class="[handleColor(color), handleSize(width, height)]" class="btn" type="submit">{{ name }}</button>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    name: String,
    color: String,
    width: String,
    height: String
  });
  
  const handleColor = (color) => {
    return color ? `btn-${color}` : 'btn';
  }
  
  const handleSize = (width, height) => {
    let classes = '';
    if (width) {
      classes += `w-${width} `;
    }
    if (height) {
      classes += `h-${height} `;
    }
    return classes.trim(); 
  }
  </script>
  
  <style scoped>
  .btn {
    transition: all 0.2s;
  }
  
  .btn:hover {
    transform: scale(1.1);
  }
  </style>
  