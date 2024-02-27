<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5"> Manage and Track all TMDD tickets</p>
          <Link :href="route('employee.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket</Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="handleAllButtonClick"></button>
            <Button :name="'Cancelled'" :color="'secondary'" class="btn-options" @click="handleCancelledButtonClick"></button>
          </div>
        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="search"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" v-model="searchQuery" placeholder="Search Tickets..."
            @input="handleSearch" aria-label="search" aria-describedby="search" />
        </div>
      </div>

      <div class="w-75">
        <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center">Ticket No</th>
              <th class="text-center">Date Created</th>
              <th class="text-center">Issue</th>
              <th class="text-center">Technician</th>
              <th class="text-center">Status</th>
              <th class="text-center">Service Report No.</th>
              <th class="text-center">Date Resolved</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in filteredTickets" :key="ticket.ticket_number">
              <td class="text-center py-3">{{ ticket.ticket_number }}</td>
              <td class="text-center py-3">{{ formatDate(ticket.created_at) }}</td>
              <td class="text-center py-3">{{ ticket.issue }}</td>
              <td class="text-center py-3">{{ ticket.technician ? ticket.technician.user.name : 'Unassigned' }}</td>
              <td class="text-center py-3"><span :class="getBadgeColor(ticket.status)" class="p-3">{{ ticket.status }}</span></td>
              <td class="text-center py-3">{{ ticket.rs_no ? ticket.rs_no : 'Unavailable' }}</td>
              <td class="text-center py-3">{{ ticket.resolved_at ? ticket.resolved_at : 'Not yet resolved' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/EmployeeHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch, onMounted } from "vue";
import Button from '@/Components/Button.vue'

const props = defineProps({
  tickets: Object,
});

const selectedStatus = ref('all');
const filteredTickets = ref(props.tickets);

let search = ref('');
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;
const tickets = ref(props.tickets);

const fetchData = () => {
  router.get(
    route('employee.tickets'),
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
  // Filter tickets to include all statuses
  filteredTickets.value = tickets.value;
};

const handleCancelledButtonClick = () => {
  console.log("Handle Cancelled Button Click");
  selectedStatus.value = 'cancelled';
  // Filter tickets to include only "Cancelled" statuses
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'Cancel');
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

const getBadgeColor = (status) => {
  switch (status.toLowerCase()) {
    case 'new':
      return 'badge text-bg-danger';
    case 'pending':
      return 'badge  text-bg-warning';
    case 'ongoing':
      return 'badge  text-bg-primary';
    case 'resolved':
      return 'badge  text-bg-success';
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

  form.put(route('employee.tickets.update.status', { ticket_id: ticket_id }));
}
</script>



<style scoped>

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
