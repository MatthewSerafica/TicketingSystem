<template>
    <canvas id="doughnut"></canvas>
</template>

<script setup>
import { Chart } from 'chart.js/auto';
import { onMounted } from 'vue';

const props = defineProps({
    service: Object,
})

onMounted(() => {
    new Chart(
        document.getElementById('doughnut'),
        {
            type: 'doughnut',
            data: {
                labels: Object.keys(props.service),
                datasets: [{
                    label: 'Service Data',
                    data: Object.values(props.service),
                    hoverOffset: 4
                }]
            }
        }
    );
})
</script>