<template>
    <div>
        <Header class="print-hidden"></Header>
        <div class="d-flex flex-row justify-content-between m-2">
            <div class="print-hidden">
                <Link :href="route('admin.users')"
                    class="print-hidden btn btn-secondary d-flex flex-row justify-content-start align-items-center"
                    style="width: 6rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-caret-left-fill print-hidden" viewBox="0 0 16 16">
                    <path
                        d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z" />
                </svg>
                <span class="">Back</span>
                </Link>
            </div>
            <div class="print-hidden">
                <button class="btn btn-primary print-hidden" @click="printPage">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                        class="bi bi-printer-fill" viewBox="0 0 16 16">
                        <path
                            d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1" />
                        <path
                            d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="container justify-content-center mt-4 col-6 print-hidden">
            <div class="row g-2 justify-content-center">
                <div class="shadow p-4 rounded text-left mb-3" v-for="department in departments"
                    :key="department.data.id">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <div class="d-flex align-items-center justify-content-center gap-4">
                            <h3 class="fw-semibold text-dark"><small>{{ department.data.department }}</small></h3>
                        </div>
                    </div>
                    <div class="gap-3 data-container justify-content-center align-items-center">
                        <div class="d-flex gap-3 data-top">
                            <div class="assigned-total card border-3 border-primary p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="fs-5 text-secondary">
                                            Request (Today)
                                        </div>
                                        <div class="fw-semibold fs-2">
                                            {{ department.data.tickets_made_today ?? 0 }} <span
                                                class="fw-normal text-secondary fs-6">request</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="resolved-total card border-3 border-success p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="fs-5 text-secondary">
                                            Resolved (Today)
                                        </div>
                                        <div class="fw-semibold fs-2">
                                            {{ department.data.tickets_resolved_today }} <span
                                                class="fw-normal text-secondary fs-6">resolved</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="resolved-total card border-3 border-info p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="fs-5 text-secondary">
                                            Complexity (Total)
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="fw-semibold fs-4">
                                                {{ department.data.complexity.simple }}
                                                <span class="fw-normal text-secondary fs-6">Simple</span>
                                            </div>
                                            <div class="fw-semibold fs-4">
                                                {{ department.data.complexity.complex }}
                                                <span class="fw-normal text-secondary fs-6">Complex</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 data-bottom">
                            <div class="assigned-today card border-3 border-danger p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="fs-5 text-secondary">
                                            Request (Total)
                                        </div>
                                        <div class="fw-semibold fs-2 ">
                                            {{ department.data.tickets_made_total ?? 0 }}
                                            <span class="fw-normal text-secondary fs-6">request</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="resolved-today card border-3 border-success p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="fs-5 text-secondary">
                                            Resolved (Total)
                                        </div>
                                        <div class="fw-semibold fs-2">
                                            {{ department.data.tickets_resolved_total ?? 0 }}
                                            <span class="fw-normal text-secondary fs-6">resolved</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="resolved-today card border-3 border-warning p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="fs-5 text-secondary">
                                            Average Resolution Time
                                        </div>
                                        <div class="fw-semibold fs-3">
                                            {{ department.data.time }}
                                            <span class="fw-normal text-secondary fs-6">Hour(s)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="print-visible">
            <table class="table table-hover shadow">
                <thead>
                    <tr class="text-center">
                        <th class="text-center text-muted">ID</th>
                        <th class="text-muted">Department</th>
                        <th class="text-muted">Request (Today)</th>
                        <th class="text-muted">Resolved (Today)</th>
                        <th class="text-muted">Request (Total)</th>
                        <th class="text-muted">Resolved (Total)</th>
                        <th class="text-muted">Complexity</th>
                        <th class="text-muted">Average Resolution Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="department in departments" class="align-middle text-center">
                        <td>{{ department.data.id }}</td>
                        <td>{{ department.data.department }}</td>
                        <td>{{ department.data.tickets_made_today }}</td>
                        <td>{{ department.data.tickets_resolved_today }}</td>
                        <td>{{ department.data.tickets_made_total }}</td>
                        <td>{{ department.data.tickets_resolved_total }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <div>
                                    Simple: {{ department.data.complexity.simple }}
                                </div>
                                <div>
                                    Complex: {{ department.data.complexity.complex }}
                                </div>
                            </div>
                        </td>
                        <td>{{ department.data.time + ' hrs' ?? '0 hrs' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    departments: Object,
})
const printPage = () => {
    window.print();
}
</script>

<style scoped>
.avatar {
    width: 2rem;
    height: 2rem;
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}

.assigned-total {
    width: 13rem;
}

.resolved-total {
    width: 13rem;
}

.assigned-today {
    width: 13rem;
}

.resolved-today {
    width: 13rem;
}

.statistics {
    display: flex;
    flex-direction: row;
    width: 70%;
}

.data-container {
    display: flex;
    flex-direction: column;
}

.print-visible {
    display: none;
}

@media (max-width: 1440px) {
    .statistics {
        width: 90%;
    }

    .doughnut {
        width: 65rem;
    }

    .bar {
        width: 70rem;
    }
}

@media (max-width: 1024px) {
    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        display: flex;
        flex-direction: column;
    }

    .doughnut {
        width: 150%;
    }

    .bar {
        width: 250%;
    }

}

@media (max-width: 768px) {
    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }

}

@media (max-width: 425px) {
    .main-content {
        margin-left: 12rem;
    }

    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        width: 250%;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }

    .data-container {
        display: flex;
        flex-direction: row;
    }

    .data-bottom {
        display: flex;
        flex-direction: column;
    }

    .data-top {
        display: flex;
        flex-direction: column;
    }

}

@media (max-width: 375px) {
    .main-content {
        margin-left: 13rem;
    }

    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        width: 280%;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }
}

@media (max-width: 320px) {
    .main-content {
        margin-left: 15rem;
    }

    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        width: 300%;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }
}

@media print {
    .print-hidden {
        display: none;
    }

    .print-visible {
        display: flex;
    }

    @page {
        size: auto;
        margin: 0;
    }
}
</style>