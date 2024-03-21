<template>
    <canvas id="chart"></canvas>
</template>

<script setup>
import { Chart } from 'chart.js/auto';
import { onMounted } from 'vue';

const props = defineProps({
    yearly: Object,
})

onMounted(() => {
    let delayed
    const yearlyReportChart = document.getElementById('chart')
    new Chart(yearlyReportChart, {
        data: {
            labels: Object.keys(props.yearly),
            datasets: [
                {
                    type: 'bar',
                    label: 'Tickets for this year',
                    data: Object.values(props.yearly),
                    backgroundColor: '#1B59F8',
                    borderColor: '#1B59F8',
                    borderWidth: 1,
                    borderRadius: 10,
                    spanGaps: true,
                    barThickness: 15,
                    hoverBackgroundColor: '#377DFF',
                    hoverBorderColor: '#377DFF',
                },
            ],
        },
        options: {
            animation: {
                onComplete: () => {
                    delayed = true
                },
                delay: (context) => {
                    let delay = 0;
                    if (context.type === 'data' && context.mode === 'default' && !delayed) {
                        delay = context.dataIndex * 300 + context.datasetIndex * 100;
                    }
                    return delay;
                },
            },
        },
    })
})
</script>