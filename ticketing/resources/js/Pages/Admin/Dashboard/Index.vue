<template>
    <div class="justify-content-center">
        <Header></Header>
        <div class="d-flex align-items-center justify-content-center" style="padding-left: 200px;">
            <!-- Left Side -->
            <div class="recent-tickets">
                <h2 class="fw-semibold">Recent Tickets</h2>
                <div class="d-flex flex-row gap-2"> <!-- Changed to flex-row -->
                    <Link class="text-decoration-none" href="/admin/tickets/create">
                        <Button class="rounded btnn secondary" value="Create Ticket"></Button>
                    </Link>
                    <Link class="text-decoration-none" href="/admin/tickets">
                        <Button class="rounded primary btnm text-warning" value="View All"></Button>
                    </Link>
                </div>
            </div>
            <!-- Right Side -->
            <div class="d-flex flex-column gap-2 align-items-center justify-content-center">
                <div v-for="ticket in tickets" :key="ticket.ticket_number" class="rounded bg-warning">
                    <Link class="text-decoration-none" :href="`/admin/tickets/${ticket.ticket_number}`">
                        <Card class="" :no="ticket.ticket_number" :issue="ticket.issue" :employee="ticket.employee.user.name"
                            :department="ticket.employee.department" :date="formatDate(ticket.created_at)"
                            :status="ticket.status"
                            :technician="ticket.technician ? ticket.technician.user.name : 'Unassigned'">
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Cards.vue';
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
.recent {
    width: auto;
    padding: 3rem;
    padding-left: 15rem;
    justify-content: center;
    align-items: center;
    gap: 1rem;
}

.recent-tickets {
    display: flex;
    width: 520px;
    height: 604px;
    flex-direction: column;
    justify-content: center;
    align-items: left;
    gap: 1rem;
    flex-shrink: 0;
}

.cards {
    display: flex;
    width: 520px;
    height: 604px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
    flex-shrink: 0;
}

.primary {
    background-color: #000066;
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
}</style>