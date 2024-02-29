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
      <div class="table-responsive">
        <table class="table table-hover shadow custom-rounded-table" style="max-width: 110rem;">
          <thead>
            <tr class="text-start">
              <th class="text-center text-muted">Ticket No</th>
              <th class="text-muted">Date Issued</th>
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
          <tbody class="">
            <tr v-for="ticket in tickets.data" :key="ticket.ticket_number" class="align-middle">
              <td class="text-center">{{ ticket.ticket_number }}</td>
              <td class="text-start">{{ formatDate(ticket.created_at) }}</td>
              <td class="text-center" style="max-width: 60px;" @click="showRRInput(ticket.rr_no, ticket.ticket_number)">
                <span v-if="!selectedRRInput || selectedRRInput !== ticket.rr_no">{{ ticket.rr_no }}</span>
                <input type="text" name="rr_no" pattern="[0-9]*" title="RR number must be numeric"   
                  v-if="selectedRow === ticket.ticket_number && selectedRRInput === ticket.rr_no"
                  v-model="editedRR[ticket.rr_no]" @blur="updateRR(ticket.rr_no, ticket.ticket_number)"
                  @keyup.enter="updateRR(ticket.rr_no, ticket.ticket_number)"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-center" style="max-width: 60px;" @click="showMSInput(ticket.ms_no, ticket.ticket_number)">
                <span v-if="!selectedMSInput || selectedMSInput !== ticket.ms_no">{{ ticket.ms_no }}</span>
                <input type="text" name="ms_no" pattern="[0-9]*" title="MS number must be numeric"
                  v-if="selectedRow === ticket.ticket_number && selectedMSInput === ticket.ms_no"
                  v-model="editedMS[ticket.ms_no]" @blur="updateMS(ticket.ms_no, ticket.ticket_number)"
                  @keyup.enter="updateMS(ticket.ms_no, ticket.ticket_number)"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-center" style="max-width: 60px;" @click="showRSInput(ticket.rs_no, ticket.ticket_number)">
                <span v-if="!selectedRSInput || selectedRSInput !== ticket.rs_no">{{ ticket.rs_no }}</span>
                <input type="text" name="rs_no" pattern="[0-9]*" title="RS number must be numeric"
                  v-if="selectedRow === ticket.ticket_number && selectedRSInput === ticket.rs_no"
                  v-model="editedRS[ticket.rs_no]" @blur="updateRS(ticket.rs_no, ticket.ticket_number)"
                  @keyup.enter="updateRS(ticket.rs_no, ticket.ticket_number)"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-start"><span class="fw-medium">{{ ticket.employee.user.name }}</span><br><small>{{
                ticket.employee.department }} - {{ ticket.employee.office }}</small></td>

              <td class="text-start text-truncate ticket-description" style="max-width: 130px;" data-hover-text="{{ ticket.description }}">
  <span class="d-inline-block" tabindex="0" :title="ticket.description">
    {{ ticket.description }}
  </span>
              </td>

              <td class="text-start">
                <div class="btn-group">
                  <button type="button" class="btn text-start" style="width: 12rem;">{{ ticket.service ? ticket.service :
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
                      @click="updateTechnician(ticket.ticket_number, technician.technician_id)">{{ technician.user.name }}
                    </li>
                  </ul>
                </div>
              </td>
              <td class="text-center" style="max-width: 100px;" @click="showSRInput(ticket.sr_no, ticket.ticket_number)">
                <span v-if="!selectedSRInput || selectedSRInput !== ticket.sr_no">{{ ticket.sr_no }}</span>
                <input type="text" name="sr_no" pattern="[0-9]*" title="SR number must be numeric"
                  v-if="selectedRow === ticket.ticket_number && selectedSRInput === ticket.sr_no"
                  v-model="editedSR[ticket.sr_no]" @blur="updateSR(ticket.sr_no, ticket.ticket_number)"
                  @keyup.enter="updateSR(ticket.sr_no, ticket.ticket_number)"
                  class="w-100 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-start">{{ isNaN(new Date(formatDate(ticket.resolved_at)))
                ? 'Not yet resolved'
                : formatDate(ticket.resolved_at) }}
              </td>
              <td class="text-start text-break" style="max-width: 120px;"
                @click="showRemarkInput(ticket.remarks, ticket.ticket_number)">
                <span v-if="!selectedRemarkInput || selectedRemarkInput !== ticket.remarks">
                  {{ ticket.remarks }}
                </span>
                <textarea v-if="selectedRow === ticket.ticket_number && selectedRemarkInput === ticket.remarks"
                  v-model="editedRemark[ticket.remarks]" @blur="updateRem(ticket.remarks, ticket.ticket_number)"
                  @keyup.enter="updateSR(ticket.sr_no, ticket.ticket_number)"
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

// Table update start
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

let selectedRRInput = ref(null);
let selectedRow = ref(null);
let editedRR = reactive({});

const showRRInput = (rrNo, ticketNumber) => {
  selectedRRInput.value = rrNo;
  selectedRow.value = ticketNumber;
  editedRR[rrNo] = rrNo ? rrNo : '';
}

const updateRR = async (rrNo, ticket_id) => {
  if (selectedRRInput.value === rrNo) {
    const editedValue = parseInt(editedRR[rrNo]);
    if (isNaN(editedValue)) {
      page.props.flash.error = 'Invalid RR number';
      return;
    }

    const form = useForm({
      rr_no: editedRR[rrNo],
    });

    try{

    await form.put(route('admin.tickets.update.rr', { ticket_id: ticket_id }));
    selectedRRInput.value = null;
    editedRR[rrNo] = '';
    
    } catch (error) {

      page.props.flash.error = 'An error occurred while updating the RR number';
    }
  }
};


let selectedMSInput = ref(null);
let editedMS = reactive({});

const showMSInput = (msNo, ticketNumber) => {
  selectedMSInput.value = msNo;
  selectedRow.value = ticketNumber;
  editedMS[msNo] = msNo ? msNo : '';
}

const updateMS = async (msNo, ticket_id) => {
  if (selectedMSInput.value === msNo) {
    if (!validateNumericInput(editedMS[msNo], 'MS')) {
      return;
    }
    const form = useForm({
      ms_no: editedMS[msNo],
    });

    await form.put(route('admin.tickets.update.ms', { ticket_id: ticket_id }));

    selectedMSInput.value = null;
    editedMS[msNo] = '';
  }
};

let selectedRSInput = ref(null);
let editedRS = reactive({});

const showRSInput = (rsNo, ticketNumber) => {
  selectedRSInput.value = rsNo;
  selectedRow.value = ticketNumber;
  editedRS[rsNo] = rsNo ? rsNo : '';
}

const updateRS = async (rsNo, ticket_id) => {
  if (selectedRSInput.value === rsNo) {
    if (!validateNumericInput(editedRS[rsNo], 'RS')) {
      return;
    }
    const form = useForm({
      rs_no: editedRS[rsNo],
    });

    await form.put(route('admin.tickets.update.rs', { ticket_id: ticket_id }));

    selectedRSInput.value = null;
    editedRS[rsNo] = '';
  }
};

let selectedSRInput = ref(null);
let editedSR = reactive({});

const showSRInput = (srNo, ticketNumber) => {
  selectedSRInput.value = srNo;
  selectedRow.value = ticketNumber;
  editedSR[srNo] = srNo ? srNo : '';
}

const updateSR = async (srNo, ticket_id) => {
  if (selectedSRInput.value === srNo) {
    if (!validateNumericInput(editedSR[srNo], 'SR')) {
      return;
    }
    const form = useForm({
      sr_no: editedSR[srNo],
    });

    await form.put(route('admin.tickets.update.sr', { ticket_id: ticket_id }));

    selectedSRInput.value = null;
    editedSR[srNo] = '';
  }
};

let selectedRemarkInput = ref(null);
let editedRemark = reactive({});

const showRemarkInput = (remark, ticketNumber) => {
  selectedRemarkInput.value = remark;
  selectedRow.value = ticketNumber;
  editedRemark[remark] = remark ? remark : '';
}

const updateRem = async (remark, ticket_id) => {
  if (selectedRemarkInput.value === remark) {
    const form = useForm({
      remark: editedRemark[remark],
    });

    await form.put(route('admin.tickets.update.remark', { ticket_id: ticket_id }));

    selectedRemarkInput.value = null;
    editedRemark[remark] = '';
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

  .ticket-description:hover::after {
    content: attr(data-hover-text);
    position: absolute;
    top: 100%;
    left: 0;
    background-color: pink;
    color: red;
    padding: 5px;
    border-radius: 5px;
    z-index: 9999;
  }
</style>