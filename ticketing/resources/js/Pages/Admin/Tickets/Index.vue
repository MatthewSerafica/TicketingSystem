<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5"> Manage and Track all TMDD tickets</p>
          <Link :href="route('admin.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket
          </Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="filterTickets('all')"></Button>
            <Button :name="'New'" :color="'secondary'" class="btn-options" @click="filterTickets('new')"></Button>
            <Button :name="'Pending'" :color="'secondary'" class="btn-options" @click="filterTickets('pending')"></Button>
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

      <div>
        <table class="table table-hover border border-secondary-subtle shadow" style="max-width: 110rem;">
          <thead>
            <tr class="text-start">
              <th class="text-center">Ticket No</th>
              <th>Date Issued</th>
              <th class="text-center">RR No</th>
              <th class="text-center">MS No</th>
              <th class="text-center">RS No</th>
              <th>Employee</th>
              <th>Office/Dept.</th>
              <th>Request</th>
              <th>Service</th>
              <th>Technician</th>
              <th class="text-center">SR No</th>
              <th>Date Resolved</th>
              <th>Remarks</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="ticket in tickets" :key="ticket.ticket_number">
              <td class="text-center py-4">{{ ticket.ticket_number }}</td>
              <td class="text-start py-4">{{ formatDate(ticket.created_at) }}</td>
              <td class="text-center py-4" style="max-width: 60px;" @click="showRRInput(ticket.rr_no)">
                <span v-if="!selectedRRInput || selectedRRInput !== ticket.rr_no">{{ ticket.rr_no }}</span>
                <input type="text" v-if="selectedRRInput === ticket.rr_no" placeholder="N/A"
                  v-model="editedRR[ticket.rr_no]" @blur="updateRR(ticket.rr_no, ticket.ticket_number)" @keyup.enter="updateRR(ticket.rr_no, ticket.ticket_number)"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-center py-4">{{ ticket.ms_no ? ticket.ms_no : 'N/A' }}</td>
              <td class="text-center py-4">{{ ticket.rs_no ? ticket.rs_no : 'N/A' }}</td>
              <td class="text-start py-4">{{ ticket.employee.user.name }}</td>
              <td class="text-start py-4">{{ ticket.employee.department }}</td>
              <td class="text-start py-4 text-truncate" style="max-width: 130px;">{{ ticket.description }}</td>
              <td class="text-start py-3">
                <div class="btn-group">
                  <button type="button" class="btn">{{ ticket.service ? ticket.service : 'Unassigned' }}</button>
                  <button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
                    aria-expanded="false" data-bs-reference="parent">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li class="dropdown-item disabled">Select a service</li>
                    <li v-for="service in services" class="btn dropdown-item"
                      @click="updateService(ticket.ticket_number, service.service)">{{ service.service }}
                    </li>
                  </ul>
                </div>
              </td>
              <td class="text-start py-3">
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
              <td class="text-center py-4">{{ ticket.sr_no ? ticket.sr_no : 'N/A' }}</td>
              <td class="text-start py-4">{{ isNaN(new Date(formatDate(ticket.resolved_at)))
                ? 'Not yet resolved'
                : formatDate(ticket.resolved_at) }}
              </td>
              <td class="text-start py-4 text-break" style="max-width: 120px;">{{ ticket.remarks ? ticket.remarks :
                'N/A' }}</td>
              <td class="text-start py-3">
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
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import moment from "moment";
import { nextTick, reactive, ref, watch } from "vue";
import Button from '@/Components/Button.vue'

const props = defineProps({
  tickets: Object,
  technicians: Object,
  filters: Object,
  services: Object,
});


let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = (type) => {
  router.get(
    route('admin.tickets'),
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
  filter.all = type === "all";
  filter.employee = type === "employee";
  filter.technician = type === "technician";
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
})

const filterTickets = async (type) => {
  console.log("Before filter change:", filter);
  if (type === "all") {
    filter.all = true;
    filter.new = false;
    filter.resolved = false;
    filter.pending = false;
  } else if (type === "new") {
    filter.all = false;
    filter.new = true;
    filter.resolved = false;
    filter.pending = false;
  } else if (type === "resolved") {
    filter.all = false;
    filter.new = false;
    filter.resolved = true;
    filter.pending = false;
  } else if (type === "pending") {
    filter.all = false;
    filter.new = false;
    filter.resolved = false;
    filter.pending = true;
  }
  await fetchData(type);

  await nextTick();
  console.log("After filter change:", filter);
}

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

const getButtonClass = (status) => {
  switch (status.toLowerCase()) {
    case 'new':
      return 'btn btn-danger';
    case 'pending':
      return 'btn btn-warning';
    case 'ongoing':
      return 'btn btn-primary';
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


const updateService = (ticket_id, service) => {
  const form = useForm({
    service: service,
  });

  form.put(route('admin.tickets.update.service', { ticket_id: ticket_id }));
}

const updateStatus = (ticket_id, status) => {
  const form = useForm({
    ticket_id: ticket_id,
    status: status
  });

  form.put(route('admin.tickets.update.status', { ticket_id: ticket_id }));
}

let selectedRRInput = ref(null);
let editedRR = reactive({});

const showRRInput = (rrNo) => {
  selectedRRInput.value = rrNo;
  editedRR[rrNo] = rrNo ? rrNo : '';
}

const updateRR = async (rrNo, ticket_id) => {
  if (selectedRRInput.value === rrNo) {
    const form = useForm({
      rr_no: editedRR[rrNo],
    });

    // Assuming you have a route like 'admin.tickets.update.rr_no'
    await form.put(route('admin.tickets.update.rr', { ticket_id: ticket_id }));

    // Reset the state
    selectedRRInput.value = null;
    editedRR[rrNo] = '';
  }
};
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
}</style>