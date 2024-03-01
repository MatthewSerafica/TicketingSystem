
<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5">Manage and Track all TMDD tickets</p>
          <Link :href="route('technician.tickets.create')">
          <Button :name="'Create New Ticket'" :color="'primary'" class="btn btn-tickets btn-primary py-2 px-5"></Button>
          </Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="filterTickets('all')"></Button>
            <Button :name="'New'" :color="'secondary'" class="btn-options" @click="filterTickets('new')"></Button>
            <Button :name="'Pending'" :color="'secondary'" class="btn-options" @click="filterTickets('pending')"></Button>
            <Button :name="'Ongoing'" :color="'secondary'" class="btn-options" @click="filterTickets('ongoing')"></Button>
            <Button :name="'Resolved'" :color="'secondary'" class="btn-options"
              @click="filterTickets('resolved')"></Button>
          </div>
        </div>

        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div class="w-75">
        <table class="table table-hover shadow custom-rounded-table">
          <thead>
            <tr class="text-start">
              <th class="text-center">Ticket No</th>
              <th>Date Issued</th>
              <th>Client</th>
              <th>Request</th>
              <th>Service</th>
              <th>SR No</th>
              <th>Date Resolved</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in tickets.data" :key="ticket.ticket_number" class="align-middle">
              <td class="text-center">{{ ticket.ticket_number }}</td>
              <td class="text-start">{{ formatDate(ticket.created_at) }}</td>
              <td class="text-start"><span class="fw-medium">{{ ticket.employee.user.name }}</span><br><small>{{
                ticket.employee.department }} - {{ ticket.employee.office }}</small></td>
  <td class="text-start text-truncate ticket-description" style="max-width: 80px;" :title="ticket.description">
    {{ ticket.description }}
  </td>
              <td class="text-start">{{ ticket.service ? ticket.service : 'Unassigned' }}</td>
              <td class="text-start" style="max-width: 20px;" @click="showSRInput(ticket.sr_no, ticket.ticket_number)">
                <span v-if="!selectedSRInput || selectedSRInput !== ticket.sr_no">{{ ticket.sr_no }}</span>
                <input type="text" v-if="selectedRow === ticket.ticket_number && selectedSRInput === ticket.sr_no"
                  v-model="editedSR[ticket.sr_no]" @blur="updateSR(ticket.sr_no, ticket.ticket_number)"
                  @keyup.enter="updateSR(ticket.sr_no, ticket.ticket_number)"
                  class="w-100 rounded border border-secondary-subtle text-start">
              </td>
              <td class="text-start">{{ ticket.resolved_at ? formatDate(ticket.resolved_at) : 'Not yet resolved' }}</td>
              <td class="text-start">
                <div class="btn-group">
                  <button type="button" :class="getButtonClass(ticket.status)" class="text-center" style="width: 5rem;">{{
                    ticket.status }}</button>
                  <button type="button" :class="getButtonClass(ticket.status)"
                    class="dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-reference="parent">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li @click="updateStatus(ticket.ticket_number, 'Pending')" class="btn dropdown-item">Pending</li>
                    <li @click="updateStatus(ticket.ticket_number, 'Ongoing')" class="btn dropdown-item">Ongoing</li>
                  </ul>
                </div>
              </td>
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
import { ref, watch, onMounted, reactive, nextTick } from "vue";
import Button from '@/Components/Button.vue'


const props = defineProps({
  tickets: Object,
  technicians: Object,
  filters: Object,
});


let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;



const fetchData = (type) => {
  router.get(
    route('technician.tickets'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      filterTickets: type,
    },
    {
      preserveState: true,
      replace: true,
    }
  )
}

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


const filter = reactive({
  all: true,
  new: false,
  resolved: false,
  pending: false,
  ongoing: false,
})

const filterTickets = async (type) => {
  console.log("Before filter change:", filter);
  if (type === "all") {
    filter.all = true;
    filter.new = false;
    filter.resolved = false;
    filter.pending = false;
    filter.ongoing = false;
  } else if (type === "new") {
    filter.all = false;
    filter.new = true;
    filter.resolved = false;
    filter.pending = false;
    filter.ongoing = false;
  } else if (type === "resolved") {
    filter.all = false;
    filter.new = false;
    filter.resolved = true;
    filter.pending = false;
    filter.ongoing = false;
  } else if (type === "pending") {
    filter.all = false;
    filter.new = false;
    filter.resolved = false;
    filter.pending = true;
    filter.ongoing = false;
  } else if (type === "ongoing") {
    filter.all = false;
    filter.new = false;
    filter.resolved = false;
    filter.pending = false;
    filter.ongoing = true;
  }
  await fetchData(type);

  await nextTick();
  console.log("After filter change:", filter);
}

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

const updateStatus = (ticket_id, status, old_status, srNo) => {
  if (status === 'Resolved') {
    if (!srNo) {
      page.props.flash.error = 'Status Update Error'
      page.props.flash.error_message = 'Please enter a Service Request Number!'
      return;
    }
  }

  const form = useForm({
    ticket_id: ticket_id,
    status: status,
    old_status: old_status,
  });

  form.put(route('technician.tickets.update.status', { ticket_id: ticket_id }));
}

let selectedSRInput = ref(null);
let selectedRow = ref(null);
let editedSR = reactive({});

const showSRInput = (srNo, ticketNumber) => {
  selectedSRInput.value = srNo;
  selectedRow.value = ticketNumber;
  editedSR[srNo] = srNo ? srNo : '';
}

const updateSR = async (srNo, ticket_id) => {
  if (selectedSRInput.value === srNo) {
    const form = useForm({
      sr_no: editedSR[srNo],
    });

    await form.put(route('technician.tickets.update.sr', { ticket_id: ticket_id }));

    selectedSRInput.value = null;
    editedSR[srNo] = '';
  }
};

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
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

.custom-rounded-table {
  border-radius: 10px;
}

.ticket-description {
  position: relative;
  cursor: default;
}


</style>
