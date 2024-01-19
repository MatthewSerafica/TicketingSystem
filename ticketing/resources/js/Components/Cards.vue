<template>
    <div class="card shadow-sm" style="width: 30rem;">
        <div class="card-body container-fluid text-center row justify-content-center align-items-center">
            <div class="col me-5">
                <div class="fw-semibold ticket row ps-4 pb-2">Ticket #{{ no }}</div>
                <div class="card-subtitle issue row ps-4 pb-2">{{ issue }}</div>
                <div class="card-subtitle employee row ps-4 pb-2">{{ employee }} / {{ department }}</div>
                <div class="card-subtitle row ps-4 pb-2 date">{{ date }}</div>
            </div>
            <div class="col">
                <div class="card-subtitle ps-2 text-secondary row date">Status</div>
                <div class="card-subtitle ps-2 pb-3 row other">{{ status }}</div>
                <div class="card-subtitle ps-2 text-secondary row date">Assigned to</div>
                <div class="card-subtitle ps-2 pb-3 row other">{{ technician }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    no: BigInt,
    issue: String,
    employee: String,
    department: String,
    status: String,
    technician: String,
    date: String,
})
</script>

<style>
* {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.card {
    display: flex;
    flex-direction: column;
}

.ticket {
    font-size: 20px;
}

.issue {
    font-size: 16px;
    font-weight: 400;
}

.employee {
    font-size: 14px;
    font-weight: 600;
}

.date {
    font-size: 14px;
}

.other {
    font-size: 16px;
    font-weight:400;
}

</style>