<template>
  <div>
    <Header class="sticky-top" ></Header>
    <!--Toast Render-->
    <div class="position-absolute end-0 mt-3 me-3" style="z-index: 100;">
      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown"
        v-if="showSuccessToast" :success="page.props.flash.success" :message="page.props.flash.message"
        @close="handleClose">
      </Toast>

      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showErrorToast"
        :error="page.props.flash.error" :error_message="page.props.flash.error_message" @close="handleClose">
      </Toast>
    </div>

    <div class="container">
      <!--Main Content-->
      <div class="main-content d-flex flex-column gap-2 justify-content-center">
        <!--CTAs and Search-->
        <div class="text-center mt-3">
          <div class="d-flex flex-column justify-content-center align-items-center gap-2">
            <h1 class="fw-bold">View All Tickets</h1>
            <p class="fs-5"> Manage and Track all TMDD Tickets</p>
            <Link :href="route('technician.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New
            Ticket
            </Link>
            <div class="d-flex gap-2 rounded">
              <VueDatePicker v-model="date" range multi-calendars :max-date="new Date()" teleport-center
                placeholder="Select date..." :enable-time-picker="false" class="border rounded border-1" />
            </div>
            <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2 flex-wrap">
              <Button :name="'All'" :color="'secondary'" class="btn-options" @click="filterTickets('all')"></Button>
              <Button :name="'New'" :color="'danger'" class="btn-options" @click="filterTickets('new')"></Button>
              <Button :name="'Pending'" :color="'warning'" class="btn-options" @click="filterTickets('pending')"></Button>
              <Button :name="'Ongoing'" :color="'info'" class="btn-options" @click="filterTickets('ongoing')"></Button>
              <Button :name="'Resolved'" :color="'success'" class="btn-options"
                @click="filterTickets('resolved')"></Button>
            </div>
          </div>
          <div class="d-flex justify-content-center align-items-center mt-3 mb-2">
            <div class="d-flex justify-content-center w-50">
              <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
                placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
            </div>
          </div>  
        </div>

        <!--Data Table-->
        <div v-if="tickets.data.length"
          class="d-flex flex-column flex-md-row align-items-md-center align-items-start gap-2 justify-content-between mt-2 mb-2 px-3 pagination">
          <div class="d-flex flex-column flex-md-row gap-2">
            <div class="d-flex flex-grow-1 gap-2 align-items-center">
              <div class="d-flex gap-2 border px-3 rounded custom-rounded-table">
                <h6 class="fw-bold text-secondary pt-3">RS-{{ rs ? rs.rs_no : 0 }}</h6>
                <h6 class="fw-bold text-secondary pt-3">MS-{{ ms ? ms.ms_no : 0 }}</h6>
                <h6 class="fw-bold text-secondary pt-3">RR-{{ rr ? rr.rr_no : 0 }}</h6>
                <h6 class="fw-bold text-secondary pt-3">SR-{{ sr ? sr.sr_no : 0 }}</h6>
              </div>
            </div>  

            <div class="d-flex flex-column flex-md-row gap-2">
              <div class="d-flex flex-grow-1 gap-2 w-100 align-items-center">
                <button class="btn btn-primary" @click="handleSort('ticket_number')">
                  <i
                    :class="{ 'bi bi-sort-up': sortColumn === 'ticket_number' && sortDirection === 'desc', 'bi bi-sort-down': sortColumn === 'ticket_number' && sortDirection === 'asc', 'bi bi-sort-down text-muted': sortColumn !== 'ticket_number' }">
                  </i>
                </button>
                <div>
                  <button v-if="!isEditable" class="btn btn-outline-primary" @click="handleEdit">
                    <i class="bi bi-pencil-square"></i>
                    Quick Edit
                  </button>
                  <button v-if="isEditable" class="btn btn-outline-primary" @click="handleEdit">
                    <i class="bi bi-x-lg"></i>
                    Close Edit
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="d-flex flex-grow-1 justify-content-md-end justify-content-start">
            <Pagination :links="tickets.links" :key="'tickets'" />
            <br>
          </div>
        </div>
        <!--Data Table-->
        <div v-if="tickets.data.length > 0" class="table-responsive rounded shadow pt-2 px-2 mb-5 overflow-auto">
          <div class="">
            <table class="table table-hover custom-rounded-table ">
              <thead>
                <tr class="text-start">
                  <th class="text-start text-muted">
                    No
                  </th>
                  <th class="text-muted">Date</th>
                  <th class="text-muted">Client</th>
                  <th class="text-muted">RS No</th>
                  <th class="text-muted">Issue/Problem</th>
                  <th class="text-muted text-center">Service</th>
                  <th class="text-center text-muted">Complexity</th>
                  <th class="text-muted center">Technician</th>
                  <th class="text-muted">SR</th>
                  <th class="text-muted">Date Resolved</th>
                  <th class="text-muted">Remarks</th>
                  <th class="text-center text-muted">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ticket in tickets.data" :key="ticket.ticket_number" class="align-middle">
                  <!-- Ticket Number -->
                  <td class="text-center" style="width: 4rem;">
                    <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    {{ ticket.ticket_number }}
                    </Link>
                  </td>
                  <!-- Date -->
                  <td class="text-start" style="width: 7rem;">
                    <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">{{ formatDate(ticket.created_at) }}
                    </Link>
                  </td>
                  <!-- Client -->
                  <td class="text-start" style="width: 12rem;">
                    <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <span class="fw-medium">
                      {{ ticket.employee.user.name }}
                    </span>
                    <br>
                    <small>{{ ticket.employee.department }} - {{ ticket.employee.office }}</small>
                    </Link>
                  </td>
                  <!-- RS -->
                  <td class="text-start">
                    <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    {{ ticket.rs_no ? ticket.rs_no : "N/A" }}
                    </Link>
                  </td>
                  <!-- Description -->
                  <td class="text-start text-truncate ticket-description" style="max-width: 120px;"
                    data-hover-text="{{ ticket.description }}">
                    <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <span class="d-inline-block" tabindex="0" :title="ticket.description">
                      {{ ticket.issue }}
                    </span>
                    </Link>
                  </td>
                  <td class="text-center">
                    <div v-if="!isEditable">
                      <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      {{ ticket.service ? ticket.service : 'N/A' }}
                      </Link>
                    </div>
                    <div v-if="isEditable">
                      <div v-if="ticket.status !== 'Resolved'" class="">
                        <button type="button" class="btn text-center" data-bs-toggle="dropdown" aria-expanded="false"
                          data-bs-reference="parent">
                          {{ ticket.service ? ticket.service : 'Unassigned' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li class="dropdown-item disabled">Select a service</li>
                          <li v-for="service in services" class="btn dropdown-item"
                            @click="updateService(ticket.ticket_number, service.service)">{{ service.service }}</li>
                        </ul>
                      </div>
                      <div v-else-if="ticket.status == 'Resolved'">
                        <span class="text-center">
                          {{ ticket.service ? ticket.service : 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </td>

                  <td class="text-center">
                    <div v-if="!isEditable">
                      <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      <button type="button" :class="getComplexityClass(ticket.complexity)" class="text-center px-3">
                        {{ ticket.complexity ? ticket.complexity : 'N/A' }}
                      </button>
                      </Link>
                    </div>
                    <div v-if="isEditable">
                      <div v-if="ticket.status !== 'Resolved'" class="">
                        <button type="button" :class="getComplexityClass(ticket.complexity)" class="text-center px-3"
                          data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                          {{ ticket.complexity ? ticket.complexity : 'N/A' }}
                        </button>
                        <ul class="dropdown-menu">
                          <li @click="updateComplexity(ticket.ticket_number, 'Simple')" class="btn dropdown-item">Simple
                          </li>
                          <li @click="updateComplexity(ticket.ticket_number, 'Complex')" class="btn dropdown-item">Complex
                          </li>
                        </ul>
                      </div>
                      <div v-else-if="ticket.status == 'Resolved'">
                        <button type="button" :class="getComplexityClass(ticket.complexity)" class="text-center px-3">
                          {{ ticket.complexity ? ticket.complexity : 'N/A' }}
                        </button>
                      </div>
                    </div>
                  </td>

                  <td class="text-start">
                    <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <div v-for="(assignedTech, index) in ticket.assigned" :key="index">
                      <div v-for="(tech, techIndex) in assignedTech.technician" :key="techIndex">
                        <span>{{ tech.user.name }}</span>
                      </div>
                    </div>
                    </Link>
                  </td>

                  <td class="text-center" style="max-width: 60px;">
                    <div v-if="!isEditable">
                      <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      <span v-if="!selectedInput || selectedInput !== 'sr' || selectedRow !== ticket.ticket_number">
                        {{ ticket.sr_no ? ticket.sr_no : 'N/A' }}
                      </span>
                      </Link>
                    </div>
                    <div v-if="isEditable" @click="showInput(ticket.sr_no, ticket.ticket_number, 'sr')">
                      <span v-if="!selectedInput || selectedInput !== 'sr' || selectedRow !== ticket.ticket_number">
                        {{ ticket.sr_no ? ticket.sr_no : 'N/A' }}
                      </span>
                      <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'sr'"
                        v-model="editData[ticket.sr_no]"
                        @blur="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no', 'sr')"
                        @keyup.enter="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no')"
                        class="w-100 rounded border border-secondary-subtle text-center">
                    </div>
                  </td>

                  <td class="text-start">
                    <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    {{ isNaN(new Date(formatDate(ticket.resolved_at))) ? 'Not yet resolved' :
            formatDate(ticket.resolved_at) }}
                    </Link>
                  </td>
                  <td class="text-start text-break" style="max-width: 120px;">
                    <div v-if="!isEditable">
                      <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      <span v-if="!selectedInput || selectedInput !== 'remarks' || selectedRow !== ticket.ticket_number">
                        {{ ticket.remarks ? ticket.remarks : 'N/A' }}
                      </span>
                      </Link>
                    </div>
                    <div v-if="isEditable" @click="showInput(ticket.remarks, ticket.ticket_number, 'remarks')">
                      <span v-if="!selectedInput || selectedInput !== 'remarks' || selectedRow !== ticket.ticket_number">
                        {{ ticket.remarks ? ticket.remarks : 'N/A' }}
                      </span>
                      <textarea v-if="selectedRow === ticket.ticket_number && selectedInput === 'remarks'"
                        v-model="editData[ticket.remarks]"
                        @blur="updateData(ticket.remarks, ticket.ticket_number, 'remarks', 'remarks')"
                        @keyup.enter="updateData(ticket.remarks, ticket.ticket_number, 'remarks', 'remarks')"
                        class="w-100 rounded border border-secondary-subtle text-center"></textarea>
                    </div>
                  </td>
                  <td class="text-start">
                    <div v-if="!isEditable">
                      <Link :href="route('technician.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      <button type="button" :class="getButtonClass(ticket.status)" class="text-center px-3">
                        {{ ticket.status }}
                      </button>
                      </Link>
                    </div>
                    <div v-if="isEditable">
                      <div class="btn-group position-static">
                        <button type="button" :class="getButtonClass(ticket.status)" class="text-center rounded-end"
                          data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                          {{ ticket.status }}
                        </button>
                        <ul class="dropdown-menu">
                          <li @click="updateStatus(ticket.ticket_number, 'New', ticket.status, ticket.sr_no)"
                            class="btn dropdown-item">New
                          </li>
                          <li @click="updateStatus(ticket.ticket_number, 'Pending', ticket.status, ticket.sr_no)"
                            class="btn dropdown-item">
                            Pending
                          </li>
                          <li @click="updateStatus(ticket.ticket_number, 'Ongoing', ticket.status, ticket.sr_no)"
                            class="btn dropdown-item">
                            Ongoing
                          </li>
                          <li @click="updateStatus(ticket.ticket_number, 'Resolved', ticket.status, ticket.sr_no)"
                            class="btn dropdown-item">
                            Resolved
                          </li>
                        </ul>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else class="mt-2 d-flex align-items-center justify-content-center">
        <EmptyCard :title="'No tickets yet...'" style="height:20rem;">
        </EmptyCard>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import EmptyCard from '@/Components/EmptyState/Table.vue';
import Pagination from '@/Components/Pagination.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import moment from "moment";
import { nextTick, reactive, ref, watch, watchEffect } from "vue";

// Toast Start
Alpine.start()

const page = usePage();

let showSuccessToast = ref(false);
let showErrorToast = ref(false);

watchEffect(() => {
  showSuccessToast.value = !!page.props.flash.success;
  showErrorToast.value = !!page.props.flash.error;
})

const handleClose = () => {
  page.props.flash.success = null;
  page.props.flash.error = null;
  showSuccessToast.value = false;
  showErrorToast.value = false;
}
// Toast End

// Define Props
const props = defineProps({
  tickets: Object,
  technicians: Object,
  filters: Object,
  services: Object,
  rr: Object,
  ms: Object,
  rs: Object,
  sr: Object,
});
// end

// Search start
let search = ref(props.filters.search);
let from_date_filter = ref(null);
let to_date_filter = ref(null);
let sortColumn = ref("ticket_number");
let sortDirection = ref("desc");
let timeoutId = null;

const fetchData = (type, all, ne, pending, ongoing, resolved) => {
  router.get(
    route('technician.tickets'),
    {
      search: search.value,
      from_date_filter: from_date_filter.value,
      to_date_filter: to_date_filter.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      filterTickets: type,
      all: all,
      new: ne,
      pending: pending,
      ongoing: ongoing,
      resolved: resolved,
    },
    {
      preserveState: true,
      replace: true,
    }
  )
  router.remember({ filter: filter });
}

const resetSorting = () => {
  console.log("Reset Sorting");
  sortColumn.value = "ticket_number"
  sortDirection.value = "desc"
}

const debouncedFetchData = () => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
  timeoutId = setTimeout(() => {
    fetchData()
  }, 500)
}

const date = ref(null);

watch([search, date], ([newSearch, newDate]) => {
  if (newDate) {
    from_date_filter.value = moment(newDate[0]).format('YYYY-MM-DD');
    to_date_filter.value = moment(newDate[1]).format('YYYY-MM-DD');
  } else {
    from_date_filter.value = null;
    to_date_filter.value = null;
  }
  if (!newSearch && !newDate) {
    resetSorting();
  }
  debouncedFetchData();
});
// Search end

// Filter start
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
  await fetchData(type, filter.all, filter.new, filter.resolved, filter.pending, filter.ongoing);

  await nextTick();
  console.log("After filter change:", filter);
}
// Filter end

// Handle edit
let isEditable = ref(false);
const handleEdit = () => {
  if (!isEditable.value) {
    isEditable.value = true;
  } else {
    isEditable.value = false;
    if (technicianCTAs.value) {
      technicianCTAs.value = false;
    }
  }
}

// Sort start
const handleSort = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
  } else {
    sortColumn.value = column;
    sortDirection.value = "asc";
  }
  fetchData();
};
// Sort end

// Table update start
const updateService = (ticket_id, service) => {
  const form = useForm({
    service: service,
  });

  form.put(route('technician.tickets.update.service', { ticket_id: ticket_id }));
}

const updateStatus = (ticket_id, status, old_status, srNo) => {
  if (status === 'Resolved') {
    if (!srNo) {
      page.props.flash.error = 'Status Update Error'
      page.props.flash.error_message = 'Please enter a Service Report Number!'
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

let selectedInput = ref(null);
let selectedRow = ref(null);
let editData = reactive({});

const showInput = (data, id, type) => {
  selectedInput.value = type;
  selectedRow.value = id;
  editData[data] = data ? data : '';
  console.log(selectedInput.value, editData[data], selectedRow.value);
}
const updateComplexity = (ticket_id, complexity) => {
  const form = useForm({
    ticket_id: ticket_id,
    complexity: complexity,
  });

  form.put(route('technician.tickets.update.complexity', { ticket_id: ticket_id }));
}

const updateData = async (data, id, updateField, type) => {
  console.log(selectedInput.value, type, editData[data], id, updateField)
  if (selectedInput.value === type) {

    if (!validateNumericInput(editData[data], updateField)) {
      return;
    }
    const form = useForm({
      [updateField]: editData[data],
      type: type,
    });

    await form.put(route('technician.tickets.update', { ticket_id: id, field: updateField }));

    selectedInput.value = null;
    editData[data] = '';


  }
};

// Table update end

// Styling and formatting
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

const getComplexityClass = (complexity) => {
  switch (complexity) {
    case 'Simple':
      return 'btn btn-warning';
    case 'Complex':
      return 'btn btn-danger';
    default:
      return 'btn btn-secondary';
  }
};

const validateNumericInput = (inputValue, propName) => {
  if (propName === 'remarks') {
    return true;
  }
  const isValid = inputValue === '' || /^\d+$/.test(inputValue);
  if (!isValid && inputValue !== '') {
    page.props.flash.error = `Invalid ${propName} number`;
    page.props.flash.message = `Please input numeric values only`;
    return false;
  }
  return true;
};


</script>

<style scoped>
.dropdown-menu {
  display: none;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.dropdown-menu.show {
  display: block;
  opacity: 1;
}

.dropdown-item {
  opacity: 0;
  transition: opacity 0.5s ease;
}

.dropdown-menu.show .dropdown-item {
  opacity: 1;
}

.dropdown-item {
  animation: fadeIn 0.5s ease forwards;
}

@keyframes fadeIn {
  0% {
    opacity: 0;
  }

  100% {
    opacity: 1;
  }
}

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