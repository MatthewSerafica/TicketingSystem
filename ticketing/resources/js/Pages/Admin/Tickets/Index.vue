<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <H1 class="fw-bold">View All Tickets</H1>
          <p class="fs-5"> Manage and Track all TMDD tickets</p>
          <Link :href="route('admin.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket
          </Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <button class="btn btn-secondary px-5 py-2">All</button>
            <button class="btn btn-secondary px-5 py-2">New</button>
            <button class="btn btn-secondary px-4 py-2">Resolved</button>
            <button class="btn btn-secondary px-4 py-2">Pending</button>
          </div>
        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
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
              <!-- <td class="text-center py-3">{{ ticket.technician ? ticket.technician.user.name : 'Unassigned' }}</td> -->
              <td class="text-center py-3">
                <div class="btn-group">
                  <button type="button" class="btn">{{ ticket.technician ? ticket.technician.user.name :
                    'Unassigned' }}</button>
                  <button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
                    aria-expanded="false" data-bs-reference="parent">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li class="dropdown-item disabled">Select a technician</li>
                    <li v-for="technician in technicians" class="btn dropdown-item"
                      @click="updateTechnician(ticket.ticket_number, technician.technician_id)">{{ technician.user.name }}
                    </li>
                  </ul>
                </div>
              </td>
              <td class="text-center py-3">
                <div class="btn-group">
                  <button type="button" :class="getButtonClass(ticket.status)">{{ ticket.status }}</button>
                  <button type="button" :class="getButtonClass(ticket.status)"
                    class="dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-reference="parent">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li @click="updateStatus(ticket.ticket_number, 'New')" class="btn dropdown-item">New</li>
                    <li @click="updateStatus(ticket.ticket_number, 'Pending')" class="btn dropdown-item">Pending</li>
                    <li @click="updateStatus(ticket.ticket_number, 'Ongoing')" class="btn dropdown-item">Ongoing</li>
                    <li @click="updateStatus(ticket.ticket_number, 'Resolved')" class="btn dropdown-item">Resolved</li>
                  </ul>
                </div>
              </td>
              <td class="text-center py-3">{{ formatDate(ticket.created_at) }}</td>
              <td class="text-center py-3">{{ ticket.resolved_at ? ticket.resolved_at : 'Not yet resolved' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div><!-- <p class="fs-5"><span :class="getBadgeClass(ticket.status)">{{ ticket.status }}</span></p> -->
</template>
<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch } from "vue";

const props = defineProps({
  tickets: Object,
  technicians: Object,
  filters: Object,
});

let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = () => {
  router.get(
    route('admin.tickets'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
    },
    {
      preserveState: true,
      replace: true,
    }
  )
}
const resetSorting = () => {
  sortColumn.value = "ticket_number"
  sortDirection.value = "asc"
}

const debouncedFetchData = () => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
  timeoutId = setTimeout(() => {
    fetchData()
  }, 500)
}

watch(search, () => {
  if (!search.value) {
    resetSorting()
  }
  debouncedFetchData();
})

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

const getButtonClass = (status) => {
  switch (status.toLowerCase()) {
    case 'new':
      return 'btn btn-danger';
    case 'pending':
      return 'btn btn-warning';
    case 'resolved':
      return 'btn btn-success';
    default:
      return 'btn btn-secondary';
  }
};

const updateTechnician = (ticket_id, technician_id) => {
  const form = useForm({
    technician_id: technician_id,
  });

  form.put(route('admin.tickets.update.technician', { ticket_id: ticket_id }));
}

const updateStatus = (ticket_id, status) => {
  const form = useForm({
    ticket_id: ticket_id,
    status: status
  });

  form.put(route('admin.tickets.update.status', { ticket_id: ticket_id }));
}
</script>

<style scoped>
.btn-tickets {
  background-color: #063970;
  color: white;
  border-color: #063970;
}
</style>