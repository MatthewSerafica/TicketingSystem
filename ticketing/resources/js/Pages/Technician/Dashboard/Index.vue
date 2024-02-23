<template>
  <div class="justify-content-center">
    <Header></Header>

    <div class="recents justify-content-center align-items-center d-flex flex-column">
      <div class="d-flex flex-row gap-5">
        <div class="recents-tickets">
          <h2 class="fw-semibold">Recent Tickets</h2>
          <div class="d-flex gap-2">
            <Link class="text-decoration-none" href="/technician/tickets/create">
              <Button class="rounded btnn secondary" value="Create Ticket"></Button>
            </Link>
            <Link class="text-decoration-none" href="/technician/tickets">
              <Button class="rounded primary btnm text-white" value="View All"></Button>
            </Link>
          </div>
        </div>

        <div class="d-flex flex-column gap-4 justify-content-center align-items-center">
          <div v-if="tickets && tickets.length > 0" class="d-flex flex-column gap-4 justify-content-center align-items-center">
            <div class="" v-for="ticket in tickets" :key="tickets.ticket_number">
                  <Link class="text-decoration-none" :href="`/technician/tickets/${ticket.ticket_number}`">
                  <Card class="" :no="ticket.ticket_number" :issue="ticket.issue"
                    :employee="ticket.employee.user.name" :department="ticket.employee.department"
                    :date="formatDate(ticket.created_at)" :status="ticket.status"
                    :technician="ticket.technician.user.name">
                  </Card>
                  </Link>
            </div>
          </div>
          <div v-else>
            <EmptyCard></EmptyCard>
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
import { Link } from '@inertiajs/vue3';
import moment from "moment";

const props = defineProps({
  tickets: Object,
})

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
</script>

<style scoped>

.recents {
  margin-top: 150px;
}

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
  color: #000000;
}

.btnn {
  transition: background-color 0.3s, color 0.3s;
}

.btnn:hover {
  background-color: #ffffff;
  color: #000000;
}
</style>