<template>
  <div>
    <Header class="sticky-top"></Header>
    <div class="position-absolute end-0 me-3" style="z-index: 100; margin-top: 5rem;">
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
      <!--Toast Render-->
      <!--Main Content-->
      <div class="main-content d-flex flex-column gap-2 justify-content-center">
        <!--CTAs and Search-->
        <div class="text-center mt-3">
          <div class="d-flex flex-column justify-content-center align-items-center gap-2">
            <h1 class="fw-bold">View All Tickets</h1>
            <p class="fs-5"> Manage and Track all TMDD Tickets</p>
            <div class="d-flex gap-2 rounded">
              <VueDatePicker v-model="date" range multi-calendars :max-date="new Date()" teleport-center
                placeholder="Select date..." :enable-time-picker="false" class="border rounded border-1" />
            </div>
            <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2 flex-wrap">
              <Button :name="'All'" :color="'secondary'" class="btn-options" @click="filterTickets('all')"></Button>
              <Button :name="'New'" :color="'danger'" class="btn-options" @click="filterTickets('new')"></Button>
              <Button :name="'Pending'" :color="'warning'" class="btn-options"
                @click="filterTickets('pending')"></Button>
              <Button :name="'Ongoing'" :color="'info'" class="btn-options" @click="filterTickets('ongoing')"></Button>
              <Button :name="'Resolved'" :color="'success'" class="btn-options"
                @click="filterTickets('resolved')"></Button>
            </div>
          </div>
          <div class="d-flex justify-content-center align-items-center mt-3 mb-2">
          <div class="input-group d-flex justify-content-center w-50 ">
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
            <div>
              <button class="btn btn-primary" @click="handleSort('ticket_number')">
                <i
                  :class="{ 'bi bi-sort-up': sortColumn === 'ticket_number' && sortDirection === 'desc', 'bi bi-sort-down': sortColumn === 'ticket_number' && sortDirection === 'asc', 'bi bi-sort-down text-muted': sortColumn !== 'ticket_number' }">
                </i>
              </button>
            </div>
          </div>
          <div class="d-flex flex-grow-1 justify-content-md-end justify-content-start">
            <Pagination :links="tickets.links" :key="'tickets'" />
            <br>
          </div>
        </div>
        <!--Data Table-->
        <div v-if="tickets.data.length" class="table-responsive rounded shadow pt-2 px-2 mb-3 overflow-auto">
          <div>
            <table class="table table-hover custom-rounded-table">
              <thead>
                <tr class="text-start">
                  <th class="text-start text-muted">
                    <div class="d-flex gap-1">
                      <span>
                        No
                      </span>

                    </div>
                  </th>
                  <th class="text-muted">Date</th>
                  <th class="text-center text-muted">RR</th>
                  <th class="text-center text-muted">MS</th>
                  <th class="text-center text-muted">RS</th>
                  <th class="text-muted">Client</th>
                  <th class="text-muted">Problem</th>
                  <th class="text-muted text-center">Service</th>
                  <th class="text-center text-muted">Complexity</th>
                  <th class="text-muted text-center" style="cursor:pointer;" @click="toggleTechnicianCTAs">Technician
                  </th>
                  <th class="text-center text-muted">SR</th>
                  <th class="text-muted">Date Done</th>
                  <th class="text-muted">Remarks</th>
                  <th class="text-start text-muted">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ticket in tickets.data" :key="ticket.ticket_number" class="align-middle">
                  <td class="text-center">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <div>{{ ticket.ticket_number }}</div>
                    </Link>
                  </td>
                  <td class="text-start" style="width: 7rem;">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    {{ formatDate(ticket.created_at) }}
                    </Link>
                  </td>
                  <td class="text-center">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <div>{{ ticket.rr_no ? ticket.rr_no : 'N/A' }}</div>
                    </Link>
                  </td>
                  <td class="text-center">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <div>{{ ticket.ms_no ? ticket.ms_no : 'N/A' }}</div>
                    </Link>
                  </td>
                  <td class="text-center">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <div>{{ ticket.rs_no ? ticket.rs_no : 'N/A' }}</div>
                    </Link>
                  </td>
                  <td class="text-start text-truncate" style="max-width: 10rem;"
                    :title="ticket.employee.user.name + '\n' + ticket.employee.department + ' - ' + ticket.employee.office">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <span class="fw-medium">
                      {{ ticket.employee.user.name }}
                    </span>
                    <br>
                    <small>{{ ticket.employee.department }} - {{ ticket.employee.office }}</small>
                    </Link>
                  </td>
                  <td class="text-start text-truncate ticket-description" style="max-width: 8rem"
                    data-hover-text="{{ ticket.issue }}{{ ticket.description }}">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <span :title="ticket.issue + '\n' + ticket.description">{{ ticket.issue }}</span>
                    </Link>
                  </td>

                  <td class="text-center">
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    <div>{{ ticket.service ? ticket.service : 'N/A' }}</div>
                    </Link>
                  </td>

                  <td class="text-center">
                    <div v-if="!isEditable">
                      <Link :href="route('observer.tickets.show', ticket.ticket_number)"
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
                          <li @click="updateComplexity(ticket.ticket_number, 'Complex')" class="btn dropdown-item">
                            Complex
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
                  <td class="text-center">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                      <div v-for="(assignedTech, index) in ticket.assigned" :key="index">
                        <div>
                          <div v-if="!isEditable">
                            <div class="d-flex flex-row justify-content-center align-items-center"
                              v-for="tech in assignedTech.technician">
                              <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                                class="text-decoration-none text-dark">
                              {{ tech.user.name }}
                              </Link>
                            </div>
                          </div>
                          <div class="btn-group position-static" v-if="isEditable">
                            <button v-if="ticket.status !== 'Resolved'" type="button"
                              class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
                              aria-expanded="false" data-bs-reference="parent"
                              @click="fetchRecommended(ticket.employee.department, ticket.ticket_number), fetchTechnicians(ticket.ticket_number)">
                              <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <div v-if="ticket.status !== 'Resolved'"
                              class="d-flex flex-row justify-content-center align-items-center"
                              v-for="(tech, techIndex) in assignedTech.technician" :key="techIndex">
                              <button type="button" class="btn text-start tech-btn" @click="toggleTechnicianCTAs">
                                {{ tech.user.name ? tech.user.name : 'N/A' }}
                              </button>
                              <button v-if="technicianCTAs"
                                class="btn align-items-center justify-content-center d-flex text-danger fs-5"
                                style="height:1.5em;" @click="removeTechnician(ticket, index, tech.technician_id)"><i
                                  class="bi bi-dash-circle-fill"></i>
                              </button>
                            </div>
                            <div v-else class="d-flex flex-row justify-content-center align-items-center"
                              v-for="tech in assignedTech.technician">
                              <button type="button" class="btn text-start tech-btn">
                                {{ tech.user.name }}
                              </button>
                            </div>
                            <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto; width: 14rem;">
                              <!--Recommended-->
                              <div>
                                <h6 class="dropdown-header">Recommended</h6>
                                <div v-for="technicians in recommended">
                                  <li v-for="technician in technicians" class="btn dropdown-item"
                                    @click="assignTechnician(ticket, index, technician)"
                                    :class="{ 'disabled': technician.tickets_assigned >= 5 }">
                                    <div class="d-flex justify-content-between">
                                      <div>
                                        <span class="fw-semibold">{{ technician.user.name }}</span>
                                        <br> <small>{{ technician.assigned_department }}</small>
                                      </div>
                                      <span>{{ technician.tickets_assigned }}</span>
                                    </div>
                                  </li>
                                </div>
                              </div>
                              <li>
                                <hr class="dropdown-divider">
                              </li>
                              <!--All-->
                              <div>
                                <h6 class="dropdown-header">All</h6>
                                <div v-for="technicians in fetchedTechs">
                                  <li v-for="technician in technicians" class="btn dropdown-item"
                                    @click="assignTechnician(ticket, index, technician)"
                                    :class="{ 'disabled': technician.tickets_assigned >= 5 }">
                                    <div class="d-flex justify-content-between">
                                      <div>
                                        <span class="fw-semibold">{{ technician.user.name }}</span>
                                        <br> <small>{{ technician.assigned_department }}</small>
                                      </div>
                                      <span>{{ technician.tickets_assigned }}</span>
                                    </div>
                                  </li>
                                </div>
                              </div>
                            </ul>
                          </div>
                        </div>
                      </div>
                      <button v-if="technicianCTAs && ticket.status !== 'Resolved' && isEditable"
                        class="btn align-items-center justify-content-center d-flex text-primary fs-5"
                        style="height:1.5em;" @click="addDropdown(ticket)">
                        <i class="bi bi-plus-circle-fill"></i>
                      </button>
                    </div>
                  </td>
                  <td class="text-center" style="max-width: 60px;">
                    <div v-if="!isEditable">
                      <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      <span v-if="!selectedInput || selectedInput !== 'sr' || selectedRow !== ticket.ticket_number">
                        {{ ticket.sr_no ? ticket.sr_no : 'N/A' }}
                      </span>
                      </Link>
                    </div>
                    <div v-if="isEditable" @click="showInput(ticket.sr_no, ticket.ticket_number, 'sr', ticket.status)">
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
                    <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                      class="text-decoration-none text-dark">
                    {{ isNaN(new Date(formatDate(ticket.resolved_at))) ? 'Not yet done' :
          formatDate(ticket.resolved_at) }}
                    </Link>
                  </td>
                  <td class="text-start text-break" style="max-width: 120px;">
                    <div v-if="!isEditable">
                      <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      <span
                        v-if="!selectedInput || selectedInput !== 'remarks' || selectedRow !== ticket.ticket_number">
                        {{ ticket.remarks ? ticket.remarks : 'N/A' }}
                      </span>
                      </Link>
                    </div>
                    <div v-if="isEditable" @click="showInput(ticket.remarks, ticket.ticket_number, 'remarks')">
                      <span
                        v-if="!selectedInput || selectedInput !== 'remarks' || selectedRow !== ticket.ticket_number">
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
                      <Link :href="route('observer.tickets.show', ticket.ticket_number)"
                        class="text-decoration-none text-dark">
                      <button type="button" :class="getButtonClass(ticket.status)" class="text-center px-3">
                        {{ ticket.status }}
                      </button>
                      </Link>
                    </div>
                    <div v-if="isEditable" class="">
                      <button type="button" :class="getButtonClass(ticket.status)" class="text-center px-3"
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
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else  class="mt-2 d-flex align-items-center justify-content-center">
          <EmptyCard :title="'No tickets yet...'"  style="height:20rem;">
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
import Header from "@/Pages/Layouts/ObserverHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import axios from 'axios';
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
  console.log('test', from_date_filter.value, to_date_filter.value)
  /* if (!from_date_filter.value) {
    from_date_filter.value = new Date().toISOString().split('T')[0];
  }
  if (!to_date_filter.value) {
    to_date_filter.value = new Date().toISOString().split('T')[0];
  } */
  router.get(
    route('observer.tickets'),
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
    from_date_filter.value = null;
    to_date_filter.value = null;
  } else {
    sortColumn.value = column;
    sortDirection.value = "asc";
    from_date_filter.value = null;
    to_date_filter.value = null;
  }
  console.log('sort', from_date_filter.value, to_date_filter.value)
  fetchData();
};
// Sort end

// Table update end

// Styling and formatting
let technicianCTAs = ref(false);

const toggleTechnicianCTAs = () => {
  if (technicianCTAs.value) {
    technicianCTAs.value = false;
  } else {
    technicianCTAs.value = true;
  }
  console.log(technicianCTAs.value)
};

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
      return 'btn btn-info';
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
.btn:hover {
  scale: 0.9;
}

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

.service-dropdown-toggle {
  border-color: transparent;
  background-color: transparent;
  color: #000;
}

.pagination {
  width: 95%;

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