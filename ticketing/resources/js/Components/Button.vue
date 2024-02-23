<template>
    <button :class="handleColor(color)" class="w-100" type="submit">{{ name }}</button>
</template>

<script setup>
const props = defineProps({
    name: String,
    color: String
})

const handleColor = (color) => {
return color ? 'btn btn-' + color : 'btn';
}
</script>

<style scoped>
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