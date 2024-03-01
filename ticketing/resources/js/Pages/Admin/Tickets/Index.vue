<template>
  <div>
    <Header></Header>
    <!--Toast Render-->
    <div class="position-absolute end-0">
      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showSuccessToast"
        :success="page.props.flash.success" :message="page.props.flash.message" @close="handleClose">
      </Toast>

      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showErrorToast"
        :error="page.props.flash.error" :error_message="page.props.flash.error_message" @close="handleClose">
      </Toast>
    </div>
    <!--Main Content-->
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <!--CTAs and Search-->
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5"> Manage and track all TMDD tickets</p>
          <Link :href="route('admin.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket
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
      <!--Data Table-->
      <div class="container">
      <div class="">
        <table class="table table-hover shadow custom-rounded-table">
          <thead>
            <tr class="text-start">
              <th class="text-start text-muted" @click="handleSort('ticket_number')">
                Ticket No
                <i :class="{
                  'bi bi-caret-up-fill': sortColumn === 'ticket_number' && sortDirection === 'asc',
                  'bi bi-caret-down-fill': sortColumn === 'ticket_number' && sortDirection === 'desc',
                  'bi bi-caret-down-fill text-muted': sortColumn !== 'ticket_number'
                }"></i>
              </th>
              <th class="text-muted">Date</th>
              <th class="text-center text-muted">RR No</th>
              <th class="text-center text-muted">MS No</th>
              <th class="text-center text-muted">RS No</th>
              <th class="text-muted">Client</th>
              <th class="text-muted">Request</th>
              <th class="text-muted">Service</th>
              <th class="text-center text-muted">Complexity</th>
              <th class="text-muted">Technician</th>
              <th class="text-center text-muted">SR No</th>
              <th class="text-muted">Date Resolved</th>
              <th class="text-muted">Remarks</th>
              <th class="text-center text-muted">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in tickets.data" :key="ticket.ticket_number" class="align-middle">
              <td class="text-center">{{ ticket.ticket_number }}</td>
              <td class="text-start">{{ formatDate(ticket.created_at) }}</td>
              <td class="text-center" style="max-width: 60px;" 
                @click="showInput(ticket.rr_no, ticket.ticket_number, 'rr')">
                <span v-if="!selectedInput || selectedInput !== 'rr' || selectedRow !== ticket.ticket_number">{{
                  ticket.rr_no }}</span>
                <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'rr'"
                  v-model="editData[ticket.rr_no]" @blur="updateData(ticket.rr_no, ticket.ticket_number, 'rr_no', 'rr')"
                  @keyup.enter="updateData(ticket.rr_no, ticket.ticket_number, 'rr_no')"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-center"  style="max-width: 60px;"
                @click="showInput(ticket.ms_no, ticket.ticket_number, 'ms')">
                <span v-if="!selectedInput || selectedInput !== 'ms' || selectedRow !== ticket.ticket_number">{{
                  ticket.ms_no }}</span>
                <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'ms'"
                  v-model="editData[ticket.ms_no]" @blur="updateData(ticket.ms_no, ticket.ticket_number, 'ms_no', 'ms')"
                  @keyup.enter="updateData(ticket.ms_no, ticket.ticket_number, 'ms_no')"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-center" style="max-width: 60px;"
                @click="showInput(ticket.rs_no, ticket.ticket_number, 'rs')">
                <span v-if="!selectedInput || selectedInput !== 'rs' || selectedRow !== ticket.ticket_number">{{
                  ticket.rs_no }}</span>
                <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'rs'"
                  v-model="editData[ticket.rs_no]" @blur="updateData(ticket.rs_no, ticket.ticket_number, 'rs_no', 'rs')"
                  @keyup.enter="updateData(ticket.rs_no, ticket.ticket_number, 'rs_no')"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-start"><span class="fw-medium">{{ ticket.employee.user.name }}</span><br><small>{{
                ticket.employee.department }} - {{ ticket.employee.office }}</small></td>

              <td class="text-start text-truncate ticket-description" style="max-width: 130px;"
                data-hover-text="{{ ticket.description }}">
                <span class="d-inline-block" tabindex="0" :title="ticket.description">
                  {{ ticket.description }}
                </span>
              </td>

              <td class="text-start">
                <div class="btn-group">
                  <button type="button" class="btn text-start" style="width: 12rem;" >{{ ticket.service ? ticket.service :
                    'Unassigned' }}</button>
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
              <td class="text-start">
                <div class="btn-group">
                  <button type="button" :class="getComplexityClass(ticket.complexity)" class="text-center"
                    style="width: 5rem;">{{
                      ticket.complexity ? ticket.complexity : 'N/A' }}</button>
                  <button type="button" :class="getComplexityClass(ticket.complexity)"
                    class="dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"
                    data-bs-reference="parent">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li @click="updateComplexity(ticket.ticket_number, 'Simple')" class="btn dropdown-item">Simple
                    </li>
                    <li @click="updateComplexity(ticket.ticket_number, 'Complex')" class="btn dropdown-item">
                      Complex</li>
                  </ul>
                </div>
              </td>
              <td class="text-start">
                <div class="btn-group">
                  <button type="button" class="btn text-start" style="width: 10rem;">{{ ticket.technician ?
                    ticket.technician.user.name :
                    'Unassigned' }}</button>
                  <button type="button" class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
                    aria-expanded="false" data-bs-reference="parent">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li class="dropdown-item disabled">Select a technician</li>
                    <li v-for="technician in technicians" class="btn dropdown-item"
                      @click="showInput(technician.technician_id, ticket.ticket_number), updateData(technician.technician_id, ticket.ticket_number, 'technician_id')">
                      {{ technician.user.name }}
                    </li>
                  </ul>
                </div>
              </td>
              <td class="text-center" style="max-width: 60px;"
                @click="showInput(ticket.sr_no, ticket.ticket_number, 'sr')">
                <span v-if="!selectedInput || selectedInput !== 'sr' || selectedRow !== ticket.ticket_number">{{
                  ticket.sr_no }}</span>
                <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'sr'"
                  v-model="editData[ticket.sr_no]" @blur="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no', 'sr')"
                  @keyup.enter="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no')"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-start">{{ isNaN(new Date(formatDate(ticket.resolved_at)))
                ? 'Not yet resolved'
                : formatDate(ticket.resolved_at) }}
              </td>
              <td class="text-start text-break" style="max-width: 120px;"
                @click="showInput(ticket.remarks, ticket.ticket_number, 'remarks')">
                <span v-if="!selectedInput || selectedInput !== 'remarks' || selectedRow !== ticket.ticket_number">
                  {{ ticket.remarks }}
                </span>
                <textarea v-if="selectedRow === ticket.ticket_number && selectedInput === 'remarks'"
                  v-model="editData[ticket.remarks]"
                  @blur="updateData(ticket.remarks, ticket.ticket_number, 'remarks', 'remarks')"
                  @keyup.enter="updateData(ticket.remarks, ticket.ticket_number, 'remarks')"
                  class="w-100 rounded border border-secondary-subtle text-center"> </textarea>
              </td>
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
                    <li @click="updateStatus(ticket.ticket_number, 'New', ticket.status, ticket.sr_no)"
                      class="btn dropdown-item">New
                    </li>
                    <li @click="updateStatus(ticket.ticket_number, 'Pending', ticket.status, ticket.sr_no)"
                      class="btn dropdown-item">
                      Pending</li>
                    <li @click="updateStatus(ticket.ticket_number, 'Ongoing', ticket.status, ticket.sr_no)"
                      class="btn dropdown-item">
                      Ongoing</li>
                    <li @click="updateStatus(ticket.ticket_number, 'Resolved', ticket.status, ticket.sr_no)"
                      class="btn dropdown-item">
                      Resolved</li>

                  </ul>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <!--Pagination-->
        <div v-if="tickets.data.length" class="flex justify-center w-full mt-6">
          <Pagination :links="tickets.links" :key="'tickets'" />
          <br>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Pagination from '@/Components/Pagination.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
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
});
// end

// Search start
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
  filter.new = type === "new";
  filter.pending = type === "pending";
  filter.pending = type === "ongoing";
  filter.pending = type === "resolved";
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
  await fetchData(type);

  await nextTick();
  console.log("After filter change:", filter);
}
// Filter end

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

  form.put(route('admin.tickets.update.service', { ticket_id: ticket_id }));
}

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

  form.put(route('admin.tickets.update.status', { ticket_id: ticket_id }));
}

const updateComplexity = (ticket_id, complexity) => {
  const form = useForm({
    ticket_id: ticket_id,
    complexity: complexity,
  });

  form.put(route('admin.tickets.update.complexity', { ticket_id: ticket_id }));
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

const updateData = async (data, id, updateField, type) => {
  console.log(selectedInput.value, editData[data], updateField)
  if (selectedInput.value === type) {

    if (!validateNumericInput(editData[data], updateField)) {
      return;
    }
    const form = useForm({
      [updateField]: editData[data],
    });

    await form.put(route('admin.tickets.update', { ticket_id: id, field: updateField }));

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
  const isValid = /^\d+$/.test(inputValue);
  if (!isValid) {
    page.props.flash.error = `Invalid ${propName} number`;
    return false;
  }
  return true;
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
}

.custom-rounded-table {
  border-radius: 10px;
}


.ticket-description {
  position: relative;
  cursor: default;
}


</style>