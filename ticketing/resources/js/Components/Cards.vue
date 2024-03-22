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
                <div class="card-subtitle ps-1 row other mb-3" style="width: 5rem;">
                    <span :class="getBadgeColor(status)" class="p-2">{{ status }}</span>
                </div>
                <div class="card-subtitle ps-2 text-secondary row date">Assigned to</div>
                <div class="card-subtitle ps-2 row other" v-for="technician in technicians">{{
                    technician.technician[0].user.name }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    no: Number,
    issue: String,
    employee: String,
    department: String,
    status: String,
    technicians: String,
    date: String,
})

const getBadgeColor = (status) => {
    switch (status.toLowerCase()) {
        case 'new':
            return 'badge text-bg-danger';
        case 'pending':
            return 'badge text-bg-warning';
        case 'ongoing':
            return 'badge text-bg-primary';
        case 'resolved':
            return 'badge text-bg-success';
        default:
            return 'badge badge-secondary';
    }
};
</script>

<style scoped>
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
    font-weight: 400;
}
</style>