
<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5">Manage and Track all TMDD tickets</p>
          <Link :href="route('technician.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket </Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="handleAllButtonClick"></Button>
            <Button :name="'New'" :color="'secondary'" class="btn-options" @click="handleNewButtonClick"></Button>
            <Button :name="'Pending'" :color="'secondary'" class="btn-options" @click="handlePendingButtonClick"></Button>
            <Button :name="'Resolved'" :color="'secondary'" class="btn-options" @click="handleResolvedButtonClick"></Button>
          </div>
    </div>

      <div class="input-group mt-3 mb-4">
        <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
      </div>
    </div>

    <div class="w-75">
      <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center">Ticket No</th>
              <th>Employee</th>
              <th>Department</th>
              <th>Issue</th>
              <th>Service</th>
              <th>Status</th> 
              <th>Date Issued</th>
              <th>Date Resolved</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in filteredTickets" :key="ticket.ticket_number">
              <td class="text-center py-3">{{ ticket.ticket_number }}</td>
              <td class="text-center py-3">{{ ticket.employee.user.name }}</td>
              <td class="text-center py-3">{{ ticket.employee.department }}</td>
              <td class="text-center py-3 text-truncate" style="max-width: 80px;">{{ ticket.issue }}</td>
              <td class="text-center py-3">{{ ticket.service ? ticket.service : 'Unassigned' }}</td>
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
              <td class="text-center py-3">{{ ticket.resolved_at ? formatDate(ticket.resolved_at) : 'Not yet resolved' }}</td>
            </tr>
          </tbody>
        </table>
    </div>
  </div>
  </div>
</template>

<script setup>

import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch, onMounted } from "vue";
import Button from '@/Components/Button.vue'


const props = defineProps({
  tickets: Object,
  technicians: Object,
  filters: Object,
});

const selectedStatus = ref('all');
const filteredTickets = ref(props.tickets);

let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;
const tickets = ref(props.tickets);



const fetchData = () => {
  router.get(
    route('technician.tickets'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      status: selectedStatus.value,
    },
    {
      preserveState: true,
      replace: true,
    }
  )
}

const handleAllButtonClick = () => {
  console.log("Handle All Button Click");
  selectedStatus.value = 'all';
  // Filter tickets to include both "Pending" and "New" statuses
  filteredTickets.value = tickets.value;
};

const handleNewButtonClick = () => {
  console.log("Handle New Button Click");
  selectedStatus.value = 'new';
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'New');
};

const handleResolvedButtonClick = () => {
  console.log("Handle Resolved Button Click");
  selectedStatus.value = 'resolved';
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'Resolved');
};

const handlePendingButtonClick = () => {
  console.log("Handle Pending Button Click");
  selectedStatus.value = 'pending';
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'Pending');
};

const resetSorting = () => {
  console.log("Reset Sorting");
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

onMounted(() => {
  handleAllButtonClick();
});

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

const updateStatus = (ticket_id, status) => {
  const { props } = usePage();
  const updatedTickets = props.tickets.map(ticket => {
    if (ticket.ticket_number === ticket_id) {
      return { ...ticket, status: status };
    }
    return ticket;
  });
  filteredTickets.value = updatedTickets;
  const form = useForm({
    ticket_id: ticket_id,
    status: status
  });

  form.put(route('technician.tickets.update.status', { ticket_id: ticket_id }));
}
</script>


<style>
.btn-tickets {
  transition: all 0.2s;
}

.btn-tickets:hover {
  transform: scale(1.1);
}
.btn-options {
  width: 100px;
}
</style>
