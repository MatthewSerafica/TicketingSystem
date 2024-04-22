<template>
    <div>
        <Header></Header>
        <div class="justify-content-center align-items-center d-flex flex-column mt-5 pt-5 gap-5 main-content">
            <div class="gap-5 justify-content-center align-items-center recent flex-wrap">
                <div class="cta">
                    <h2 class="fw-semibold">Recent Tickets</h2>
                    <div class="d-flex gap-2 flex-wrap">
                        <Link class="text-decoration-none" :href="route('technician.tickets.create')">
                            <Button :name="'Create'" :color="'primary'" class="btn-width"></Button>
                        </Link>
                        <Link class="text-decoration-none" :href="route('technician.tickets')">
                            <Button :name="'View All'" :color="'outline-primary'" class="btn-width"></Button>
                        </Link>
                    </div>
                </div>
                <div class="d-flex flex-column gap-4 justify-content-center align-items-center ms-md-5">
                    <div v-if="tickets && tickets.length > 0"
                        class="d-flex flex-column gap-4 justify-content-center align-items-center">
                        <div class="" v-for="ticket in tickets" :key="tickets.ticket_number">
                            <Link class="text-decoration-none" :href="route('technician.tickets')">
                                <Card class="text-truncate hover" :no="ticket.ticket_number" :issue="ticket.issue"
                                    :employee="ticket.employee.user.name" :department="ticket.employee.department"
                                    :date="formatDate(ticket.created_at)" :status="ticket.status"
                                    :technicians="ticket.assigned ? ticket.assigned : 'Unassigned'">
                                </Card>
                            </Link>
                        </div>
                    </div>
                    <div v-else>
                        <EmptyCard :title="'No tickets yet...'"></EmptyCard>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column w-100 justify-content-center align-items-center text-center">
                <h1 class="stat-label">Statistics</h1>
                <div class="w-100 justify-content-center align-items-center gap-5 m-3 statistics flex-wrap">
                    <div class="card p-5 shadow-sm">
                        <Doughnut :service="service" class="doughnut"></Doughnut>
                    </div>
                    <div class="card p-5 shadow-sm">
                        <Bar :yearly="yearly_data" class="bar"></Bar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Cards.vue';
import EmptyCard from '@/Components/EmptyState/Cards.vue';
import Header from '@/Pages/Layouts/TechnicianHeader.vue';
import Bar from "@/Pages/Technician/Dashboard/Charts/Bar.vue";
import Doughnut from "@/Pages/Technician/Dashboard/Charts/Doughnut.vue";
import { Link } from '@inertiajs/vue3';
import moment from "moment";

const props = defineProps({
    tickets: Object,
    yearly_data: Object,
    service: Object,
})

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
</script>

<style scoped>
.hover:hover {
    background-color: white;
    transition: transform 0.5s ease;
    transform: scale(1.1); 
}

.btn-width {
    width: 150px;
    height: 50px;
}
.doughnut {
    width: 20rem;
}

.statistics {
    display: flex;
    flex-direction: row;
}

.recent {
    display: flex;
    flex-direction: row;
}

.bar {
    width: 40rem;
}

@media (max-width: 768px) {
    .main-content {
        width: 80%;
    }
}

@media (max-width: 425px) {
    .main-content {
        width: 90%;
    }
}

@media (max-width: 320px) {
    .main-content {
        width: 100%;
    }
    .statistics {
        flex-direction: column;
    }
    .bar {
        width: 100%;
    }
    .doughnut {
        width: 100%;
    }
}
</style>
