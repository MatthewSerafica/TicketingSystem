<template>
  <div>
    <Header></Header>
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
    <!--Main Content-->
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center main-content">
      <!--CTAs and Search-->
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5"> Manage and track all TMDD tickets</p>
          <Link :href="route('technician.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New
          Ticket
          </Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="filterTickets('all')"></Button>
            <Button :name="'New'" :color="'secondary'" class="btn-options" @click="filterTickets('new')"></Button>
            <Button :name="'Pending'" :color="'secondary'" class="btn-options"
              @click="filterTickets('pending')"></Button>
            <Button :name="'Ongoing'" :color="'secondary'" class="btn-options"
              @click="filterTickets('ongoing')"></Button>
            <Button :name="'Resolved'" :color="'secondary'" class="btn-options"
              @click="filterTickets('resolved')"></Button>
          </div>
        </div>
        <div class="input-group mt-3">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>
      <!--Data Table-->
      <div v-if="tickets.data.length" class="d-flex justify-content-end mb-2 mt-3 pagination">
        <Pagination :links="tickets.links" :key="'tickets'" />
        <br>
      </div>
      <div class="table-responsive rounded shadow pt-2 px-2 mb-5">
        <div class="">
          <table class="table table-hover custom-rounded-table">
            <thead>
              <tr class="text-start">
                <th class="text-start text-muted" @click="handleSort('ticket_number')">
                  No
                  <i
                    :class="{ 'bi bi-caret-up-fill': sortColumn === 'ticket_number' && sortDirection === 'asc', 'bi bi-caret-down-fill': sortColumn === 'ticket_number' && sortDirection === 'desc', 'bi bi-caret-down-fill text-muted': sortColumn !== 'ticket_number' }"></i>
                </th>
                <th class="text-muted">Date</th>
                <th class="text-muted">Client</th>
                <th class="text-muted">Request</th>
                <th class="text-muted">Service</th>
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
                <td class="text-center">{{ ticket.ticket_number }}</td>
                <td class="text-start">{{ formatDate(ticket.created_at) }}</td>
                <td class="text-start"><span class="fw-medium">
                    {{ ticket.employee.user.name }}</span><br>
                  <small>{{ ticket.employee.department }} - {{ ticket.employee.office }}</small>
                </td>
                <td class="text-start text-truncate ticket-description" style="max-width: 130px;"
                  data-hover-text="{{ ticket.description }}">
                  <span class="d-inline-block" tabindex="0" :title="ticket.description">
                    {{ ticket.description }}
                  </span>
                </td>

                <td class="text-start text-truncate ticket-description" style="max-width: 130px;"
                  data-hover-text="{{ ticket.service }}">
                  <span class="d-inline-block" tabindex="0" :title="ticket.service">
                    {{ ticket.service }}
                  </span>
                </td>

                <td class="text-center ticket-description">
                  <button type="button" :class="getComplexityClass(ticket.complexity)" class="text-center px-3">
                    {{ ticket.complexity ? ticket.complexity : 'N/A' }}
                  </button>
                </td>

                <td class="text-start">
                  <div v-for="(assignedTech, index) in ticket.assigned" :key="index">
                    <div v-for="(tech, techIndex) in assignedTech.technician" :key="techIndex">
                      <span>{{ tech.user.name }}</span>
                    </div>
                  </div>
                </td>

                <td class="text-center" style="max-width: 20px;"
                  @click="showInput(ticket.sr_no, ticket.ticket_number, 'sr')">
                  <span v-if="!selectedInput || selectedInput !== ticket.sr_no">{{ ticket.sr_no }}</span>
                  <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'sr'"
                    v-model="editData[ticket.sr_no]"
                    @blur="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no', 'sr')"
                    @keyup.enter="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no')"
                    class="w-100 rounded border border-secondary-subtle text-center">
                </td>

                <td class="text-start">
                  {{ isNaN(new Date(formatDate(ticket.resolved_at))) ? 'Not yet resolved' :
          formatDate(ticket.resolved_at) }}
                </td>
                <td class="text-start text-break" style="max-width: 120px;"
                  @click="showInput(ticket.remarks, ticket.ticket_number, 'remarks')">
                  <span v-if="!selectedInput || selectedInput !== 'remarks' || selectedRow !== ticket.ticket_number">
                    {{ ticket.remarks ? ticket.remarks : 'N/A' }}
                  </span>
                  <textarea v-if="selectedRow === ticket.ticket_number && selectedInput === 'remarks'"
                    v-model="editData[ticket.remarks]"
                    @blur="updateData(ticket.remarks, ticket.ticket_number, 'remarks', 'remarks')"
                    @keyup.enter="updateData(ticket.remarks, ticket.ticket_number, 'remarks', 'remarks')"
                    class="w-100 rounded border border-secondary-subtle text-center"></textarea>
                </td>
                <td class="text-start">
                  <div class="btn-group position-static">
                    <button type="button" :class="getButtonClass(ticket.status)" class="text-center rounded-end"
                      data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                      {{ ticket.status }}
                    </button>

                    <ul v-if="ticket.status !== 'Resolved'" class="dropdown-menu">
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
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
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
});
// end

// Search start
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


const updateData = async (data, id, updateField, type) => {
  console.log(selectedInput.value, type, editData[data], updateField)
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
.table-responsive {
  width: 90%;
  overflow-x: auto;
}

.pagination {
  width: 90%;
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

@media (max-width: 1440px) {
  .custom-rounded-table {
    font-size: 12px;
  }


  .table-responsive {
    width: 85rem;
    overflow-x: auto;
  }

  .pagination {
    width: 85rem;
  }

  .btn-options {
    padding: 6px 0;
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media (max-width: 1024px) {
  .custom-rounded-table {
    font-size: 12px;
  }

  .table-responsive {
    width: 55rem;
    overflow-x: auto;
  }

  .pagination {
    width: 55rem;
  }

  .btn-options {
    padding: 6px 0;
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media (max-width: 768px) {
  .custom-rounded-table {
    font-size: 12px;
  }

  .pagination {
    width: 40rem;
  }

  .table-responsive {
    width: 40rem;
    overflow-x: auto;
  }

  .btn-options {
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media (max-width: 425px) {

  .main-content {
    margin-left: 12rem;
  }

  .custom-rounded-table {
    font-size: 12px;
  }

  .pagination {
    width: 35rem;
  }

  .table-responsive {
    width: 35rem;
    overflow-x: auto;
  }

  .btn-options {
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media (max-width: 375px) {

  .main-content {
    margin-left: 13rem;
  }
}

@media (max-width: 325px) {

  .main-content {
    margin-left: 15rem;
  }
}
</style>