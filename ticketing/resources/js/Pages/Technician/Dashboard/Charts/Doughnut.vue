<template>
    <canvas id="doughnut"></canvas>
</template>

<script setup>
import { Chart } from 'chart.js/auto';
import { onMounted } from 'vue';

onMounted(() => {
    new Chart(
        document.getElementById('doughnut'),
        {
            type: 'doughnut',
            data: {
                labels: [
                    'Hardware Repair',
                    'Software Repair',
                    'Maintenance'
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [300, 50, 100],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    hoverOffset: 4
                }]
            }
        }
    );
})
</script>