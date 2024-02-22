
<template>
  <div>
    <Header></Header>
    <br>
    {{ ticket }}
    <div class="search">
      <input type="text" v-model="searchQuery" placeholder="Search Tickets..." @input="handleSearch" />
    </div>
    {{ tickets }}
    <div class="container text-center w-100 h-100 justify-center">
      <h1>View All Tickets</h1>
      <p>Manage and Track all TMDD tickets</p>
      <Link :href="`/technician/tickets/create`" class="create-ticket-link">Create New Ticket</Link>
      <br><br>
      <button class="ticket-button" @click="filterTickets('All')">All</button>
      <button class="ticket-button" @click="filterTickets('New')">New</button>
      <button class="ticket-button" @click="filterTickets('Pending')">Pending</button>
      <button class="ticket-button" @click="filterTickets('Resolved')">Resolved</button>
    </div>

    <div class="table-container">
      <table class="table table-striped">
        <thead class="">
          <tr class="text-center">
            <th>Ticket No</th>
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
            <td class="text-center py-3">
              <Link class="text-decoration-none text-dark p-3"
                :href="route('technician.tickets.show', ticket.ticket_number)">{{ ticket.ticket_number }}</Link>
            </td>
            <td class="text-center py-3">
              <Link class="text-decoration-none text-dark p-3"
                :href="route('technician.tickets.show', ticket.ticket_number)">
              {{ ticket.employee.user.name }}</Link>
            </td>
            <td class="text-center py-3">
              <Link class="text-decoration-none text-dark p-3"
                :href="route('technician.tickets.show', ticket.ticket_number)">{{ ticket.employee.department }}</Link>
            </td>
            <td class="text-center py-3">
              <Link class="text-decoration-none text-dark p-3"
                :href="route('technician.tickets.show', ticket.ticket_number)">{{ ticket.issue }}</Link>
            </td>
            <td class="text-center py-3">
              <Link class="text-decoration-none text-dark p-3"
                :href="route('technician.tickets.show', ticket.ticket_number)">{{ ticket.service ? ticket.service :
                  'Unassigned' }}</Link>
            </td>
            <td class="text-center py-3">
              <div class="btn-group">
                <button type="button" :class="getButtonClass(ticket.status)">{{ ticket.status }}</button>
                <button type="button" :class="getButtonClass(ticket.status)" class="dropdown-toggle dropdown-toggle-split"
                  data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
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
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch, onMounted } from "vue";

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
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'New' || ticket.status === 'Pending');
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
  handleAllButtonClick(); // Call the method to display all tickets initially
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
  const form = useForm({
    ticket_id: ticket_id,
    status: status
  });

  form.put(route('admin.tickets.update.status', { ticket_id: ticket_id }));
}
</script>


<style>
* {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}


.search {
  margin: 10px 0;
  display: flex;
  justify-content: center;
}

.search input {
  width: 50%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.container {
  padding: 20px;
}

h1 {
  font-size: 36px;
  margin-bottom: 10px;
}

p {
  font-size: 16px;
  margin-bottom: 20px;
}

Link.create-ticket-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #000000;
  /* Green */
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  transition: background-color 0.3s;
}

Link.create-ticket-link:hover {
  background-color: #898989;
}

.ticket-button {
  width: 10%;
  margin-right: 10px;
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  background-color: #cecece;
  color: #1e1e1e;
  border-radius: 8px;
  /* Adjust border-radius for rounded edges */
  cursor: pointer;
  transition: background-color 0.3s;
}

.ticket-button:hover {
  background-color: #898989;
  color: #e7e7e7;
}

.btn-tickets {
  background-color: #063970;
  color: white;
  border-color: #063970;
}</style>
