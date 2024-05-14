<template>
    <div>
        <Header class="print-hidden"></Header>
        <div class="d-flex flex-row justify-content-between m-2">
            <div class="print-hidden">
                <Link :href="route('observer.users')"
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
        </div>
        <div class="container justify-content-center mt-4 col-6 print-hidden">
            <div class="row g-2 justify-content-center">
                <div class="shadow p-4 rounded text-left mb-3" v-for="user in users" :key="user.data.id">
                    <div class="d-flex align-items-center gap-2">
                        <div>
                            <img v-if="user.data.avatar !== 'http://127.0.0.1:8000/storage'" :src="user.data.avatar"
                                alt="User profile picture" class="avatar rounded-circle">
                            <EmptyProfile v-else class="avatar rounded-circle">
                            </EmptyProfile>
                        </div>
                        <div class="d-flex flex-row gap-2 mt-3">
                            <div class="d-flex align-items-center justify-content-center gap-4">
                                <p class="fw-semibold text-dark"><small>{{ user.data.name }}</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="gap-3 data-container justify-content-center align-items-center">
                        <div class="d-flex gap-3 data-top">
                            <div class="assigned-total card border-3 border-primary p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="fs-5 text-secondary">
                                            Assigned (Total)
                                        </div>
                                        <div class="fw-semibold fs-2">
                                            {{ user.data.technician.assigned ?? 0 }} <span
                                                class="fw-normal text-secondary fs-6">assigned</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="resolved-total card border-3 border-success p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="fs-5 text-secondary">
                                            Resolved (Total)
                                        </div>
                                        <div class="fw-semibold fs-2">
                                            {{ user.data.technician.resolved }} <span
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
                                                {{ user.data.complexity_counts.simple }}
                                                <span class="fw-normal text-secondary fs-6">Simple</span>
                                            </div>
                                            <div class="fw-semibold fs-4">
                                                {{ user.data.complexity_counts.complex }}
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
                                            Assigned (Today)
                                        </div>
                                        <div class="fw-semibold fs-2 ">
                                            {{ user.data.assigned_today }}
                                            <span class="fw-normal text-secondary fs-6">assigned</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="resolved-today card border-3 border-success p-2 shadow"
                                style="border-top: 1px; border-left: 1px; border-right: 1px;">
                                <div class="card-body d-flex flex-column gap-4">
                                    <div class="d-flex flex-column gap-3">
                                        <div class="fs-5 text-secondary">
                                            Resolved (Today)
                                        </div>
                                        <div class="fw-semibold fs-2">
                                            {{ user.data.resolved_today }}
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
                                            {{ user.data.average_resolution_time }}
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
                        <th class="text-muted">Name</th>
                        <th class="text-muted">Assigned (Total)</th>
                        <th class="text-muted">Resolved (Total)</th>
                        <th class="text-muted">Assigned (Today)</th>
                        <th class="text-muted">Resolved (Today)</th>
                        <th class="text-muted">Complexity</th>
                        <th class="text-muted">Average Resolution Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" class="align-middle text-center">
                        <td>{{ user.data.id }}</td>
                        <td>{{ user.data.name }}</td>
                        <td>{{ user.data.technician.assigned }}</td>
                        <td>{{ user.data.technician.resolved }}</td>
                        <td>{{ user.data.assigned_today }}</td>
                        <td>{{ user.data.resolved_today }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <div>
                                    Simple: {{ user.data.complexity_counts.simple }}
                                </div>
                                <div>
                                    Complex: {{ user.data.complexity_counts.complex }}
                                </div>
                            </div>
                        </td>
                        <td>{{ user.data.average_resolution_time + ' hrs' ?? '0 hrs' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/ObserverHeader.vue";
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    users: Object,
})
const printPage = () => {
    window.print();
}
</script>

<style scoped>
.avatar {
    width: 2rem;
    /* Width of the avatar */
    height: 2rem;
    /* Height of the avatar */
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