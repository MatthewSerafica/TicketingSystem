<template>
    <input class="button" type="button">
</template>

<script setup>
const props = defineProps({
    text: String,
})
</script>

<style>
.button {
    display: flex;
    width: 160px;
    padding: 12px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 8px;
    border: 1px solid rgba(0, 0, 0, 0.50);
}
</style>