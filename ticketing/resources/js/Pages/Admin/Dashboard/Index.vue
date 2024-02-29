<template>
    <div class="justify-content-center">
        <Header></Header>
        <div class="justify-content-center align-items-center d-flex flex-column mt-5 pt-5 gap-5">
            <div class="d-flex flex-row gap-5 justify-content-center align-items-center">
                <div>
                    <h2 class="fw-semibold">Recent Tickets</h2>
                    <div class="d-flex gap-2">
                        <Link class="text-decoration-none" :href="route('admin.tickets.create')">
                        <Button :name="'Create'" :color="'primary'" class="btn-width"></Button>
                        </Link>
                        <Link class="text-decoration-none" :href="route('admin.tickets')">
                        <Button :name="'View All'" :color="'secondary'" class="btn-width"></Button>
                        </Link>
                    </div>
                </div>
                <div class="d-flex flex-column gap-4 justify-content-center align-items-center ms-5">
                    <div v-if="tickets && tickets.length > 0"
                        class="d-flex flex-column gap-4 justify-content-center align-items-center">
                        <div class="" v-for="ticket in tickets" :key="tickets.ticket_number">
                            <Link class="text-decoration-none" :href="route('admin.tickets')">
                            <Card class="text-truncate" :no="ticket.ticket_number" :issue="ticket.issue"
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
            <div class="d-flex flex-column w-100 justify-content-center align-items-center">
                <h1>Statistics</h1>
                <div class="d-flex flex-row w-100 justify-content-center align-items-center gap-5 m-5">
                    <div class="card p-5">
                        <Doughnut style="width: 20rem;"></Doughnut>
                    </div>
                    <div class="card p-5">
                        <Bar style="width: 40rem;"></Bar>
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
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { Link } from '@inertiajs/vue3';
import moment from "moment";
import Bar from "@/Pages/Admin/Dashboard/Charts/Bar.vue"
import Doughnut from "@/Pages/Admin/Dashboard/Charts/Doughnut.vue"

const props = defineProps({
    tickets: Object,
})

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
</script>

<style>
.btn-width {
    width: 150px;
    height: 50px;
}
</style>