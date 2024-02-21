<template>
  <Header></Header>
  <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
    <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
      <div class="d-flex flex-column justify-content-center align-items-center gap-2">
        <H1 class="fw-bold">View All Tickets</H1>
        <p class="fs-5"> Manage and Track all TMDD tickets</p>
        <Link :href="route('admin.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket</Link>
        <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
          <button class="btn btn-secondary px-5 py-2">All</button>
          <button class="btn btn-secondary px-5 py-2">New</button>
          <button class="btn btn-secondary px-4 py-2">Resolved</button>
          <button class="btn btn-secondary px-4 py-2">Pending</button>
        </div>
      </div>
      <div class="input-group mt-3 mb-4">
        <span class="input-group-text" id="search"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control py-2" v-model="searchQuery" placeholder="Search Tickets..."
          @input="handleSearch" aria-label="search" aria-describedby="search" />
      </div>
    </div>
    <div class="w-75">
      <table class="table table-striped">
        <thead class="">
          <tr class="text-center">
            <th>Ticket No</th>
            <th>Employee</th>
            <th>Department</th>
            <th>Issue</th>
            <th>Service</th>
            <th>Technician</th>
            <th>Status</th>
            <th>Date Issued</th>
            <th>Date Resolved</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in tickets" :key="tickets.ticket_number">
            <td class="text-center py-3">{{ ticket.ticket_number }}</td>
            <td class="text-center py-3">{{ ticket.employee.user.name }}</td>
            <td class="text-center py-3">{{ ticket.employee.department }}</td>
            <td class="text-center py-3">{{ ticket.issue }}</td>
            <td class="text-center py-3">{{ ticket.service ? ticket.service : 'Unassigned' }}</td>
            <td class="text-center py-3">{{ ticket.technician ? ticket.technician.user.name : 'Unassigned' }}</td>
            <td class="text-center py-3"><p class="fs-5"><span :class="getBadgeClass(ticket.status)">{{ ticket.status }}</span></p></td>
            <td class="text-center py-3">{{ formatDate(ticket.created_at) }}</td>
            <td class="text-center py-3">{{ ticket.resolved_at ? ticket.resolved_at : 'Not yet resolved' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link } from "@inertiajs/vue3";
import moment from "moment";

const props = defineProps({
  tickets: Object,
})


const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

const getBadgeClass = (status) => {
  switch (status.toLowerCase()) {
    case 'new':
      return 'badge text-bg-danger p-2';
    case 'pending':
      return 'badge text-bg-warning p-2';
    case 'resolved':
      return 'badge text-bg-success p-2';
    // Add more cases for other statuses if needed
    default:
      return 'badge text-bg-secondary p-2';
  }
};
</script>

<style scoped>
.btn-tickets {
  background-color: #063970;
  color: white;
  border-color: #063970;
}
</style>