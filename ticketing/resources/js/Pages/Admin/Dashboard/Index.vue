<template>
    <div class="justify-content-center">
        <Header></Header>
        <div class="justify-content-center align-items-center d-flex flex-column mt-5 pt-5 gap-5">
            <div class="d-flex flex-row gap-5">
                <div class="mt-5 pt-5">
                    <h2 class="fw-semibold">Recent Tickets</h2>
                    <div class="d-flex gap-2">
                        <Link class="text-decoration-none" :href="route('admin.tickets.create')">
                        <Button class="rounded btnn secondary" value="Create Ticket"></Button>
                        </Link>
                        <Link class="text-decoration-none" :href="route('admin.tickets')">
                        <Button class="rounded primary btnm text-light" value="View All"></Button>
                        </Link>
                    </div>
                </div>
                <div class="d-flex flex-column gap-4 justify-content-center align-items-center">
                    <div v-if="tickets && tickets.length > 0" class="d-flex flex-column gap-4 justify-content-center align-items-center">
                        <div class="" v-for="ticket in tickets" :key="tickets.ticket_number">
                            <Link class="text-decoration-none" :href="`/admin/tickets/${ticket.ticket_number}`">
                            <Card class="" :no="ticket.ticket_number" :issue="ticket.issue"
                                :employee="ticket.employee.user.name" :department="ticket.employee.department"
                                :date="formatDate(ticket.created_at)" :status="ticket.status"
                                :technician="ticket.technician ? ticket.technician.user.name : 'Unassigned'">
                            </Card>
                            </Link>
                        </div>
                    </div>
                    <div v-else>
                        <EmptyCard></EmptyCard>
                    </div>
                </div>
            </div>
            <div>
                <h1>Statistics</h1>
            </div>
        </div>
    </div>
</template>


<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Cards.vue';
import EmptyCard from '@/Components/EmptyState/Cards.vue';
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { Link } from '@inertiajs/vue3';
import moment from "moment";

const props = defineProps({
    tickets: Object,
})

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
</script>

<style>
.primary {
    background-color: #063970;
}

.btnm {
    transition: background-color 0.3s, color 0.3s;
}

.btnm:hover {
    background-color: #00009c;
    color: #CC9900;
}

.secondary {
    background-color: #efefef;
}

.btnn {
    transition: background-color 0.3s, color 0.3s;
}

.btnn:hover {
    background-color: #ffffff;
    color: #000000;
}
</style>