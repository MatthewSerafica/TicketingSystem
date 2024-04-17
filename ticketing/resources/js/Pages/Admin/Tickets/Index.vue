<template>
  <div>
    <Header class="sticky-top" style="z-index: 110;"></Header>
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
      <div class="text-center justify-content-center align-items-center d-flex mt-3 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5"> Manage and Track all TMDD Tickets</p>
          <Link :href="route('admin.tickets.create')" class="btn btn-tickets btn-primary py-2 px-5">
          Create New Ticket
          </Link>
          <div class="d-flex gap-2 rounded">
            <VueDatePicker v-model="date" range multi-calendars :max-date="new Date()" teleport-center
              placeholder="Select date..." :enable-time-picker="false" class="border rounded border-1" />
          </div>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="filterTickets('all')"></Button>
            <Button :name="'New'" :color="'danger'" class="btn-options" @click="filterTickets('new')"></Button>
            <Button :name="'Pending'" :color="'warning'" class="btn-options" @click="filterTickets('pending')"></Button>
            <Button :name="'Ongoing'" :color="'info'" class="btn-options" @click="filterTickets('ongoing')"></Button>
            <Button :name="'Resolved'" :color="'success'" class="btn-options"
              @click="filterTickets('resolved')"></Button>
          </div>
        </div>
        <div class="input-group mt-3 mb-2">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div v-if="tickets.data.length"
        class="d-flex align-items-center justify-content-between mt-2 mb-2 px-3 pagination">
        <div class="d-flex flex-grow-1 gap-2 w-100">
          <div class="d-flex gap-2 border px-3 rounded">
            <p class="fw-bold text-secondary pt-3">RS - {{ rs ? rs.rs_no : 0 }} |</p>
            <p class="fw-bold text-secondary pt-3">MS - {{ ms ? ms.ms_no : 0 }} |</p>
            <p class="fw-bold text-secondary pt-3">RR - {{ rr ? rr.rr_no : 0 }}</p>
          </div>
        </div>
        <div class="d-flex flex-grow-1">
          <Pagination :links="tickets.links" :key="'tickets'" />
          <br>
        </div>
      </div>
      <!--Data Table-->
      <div v-if="tickets.data.length" class="table-responsive rounded shadow pt-2 px-2 mb-3">
        <div class="d-flex justify-content-center align-items-center pb-2">
          <table class="table table-hover custom-rounded-table">
            <thead>
              <tr class="text-start">
                <th class="text-start text-muted" @click="handleSort('ticket_number')">
                  <div class="d-flex gap-1">
                    <span>
                      No
                    </span>
                    <i
                      :class="{ 'bi bi-caret-up-fill': sortColumn === 'ticket_number' && sortDirection === 'desc', 'bi bi-caret-down-fill': sortColumn === 'ticket_number' && sortDirection === 'asc', 'bi bi-caret-down-fill text-muted': sortColumn !== 'ticket_number' }"></i>
                  </div>
                </th>
                <th class="text-muted">Date</th>
                <th class="text-center text-muted">RR</th>
                <th class="text-center text-muted">MS</th>
                <th class="text-center text-muted">RS</th>
                <th class="text-muted">Client</th>
                <th class="text-muted">Problem</th>
                <th class="text-muted text-center">Service</th>
                <th class="text-start text-muted">Complexity</th>
                <th class="text-muted text-center" style="cursor:pointer;" @click="toggleTechnicianCTAs">Technician</th>
                <th class="text-center text-muted">SR</th>
                <th class="text-muted">Date Done</th>
                <th class="text-muted">Remarks</th>
                <th class="text-center text-muted">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ticket in tickets.data" :key="ticket.ticket_number" class="align-middle">
                <td class="text-center">{{ ticket.ticket_number }}</td>
                <td class="text-start" style="width: 7rem;">{{ formatDate(ticket.created_at) }}</td>
                <td class="text-center" style="max-width: 60px;"
                  @click="showInput(ticket.rr_no, ticket.ticket_number, 'rr')">
                  <span v-if="!selectedInput || selectedInput !== 'rr' || selectedRow !== ticket.ticket_number">
                    {{ ticket.rr_no ? ticket.rr_no : 'N/A' }}
                  </span>
                  <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'rr'"
                    v-model="editData[ticket.rr_no]"
                    @blur="updateData(ticket.rr_no, ticket.ticket_number, 'rr_no', 'rr')"
                    @keyup.enter="updateData(ticket.rr_no, ticket.ticket_number, 'rr_no', 'rr')"
                    class="w-100 rounded border border-secondary-subtle text-center">
                </td>
                <td class="text-center" style="max-width: 60px;"
                  @click="showInput(ticket.ms_no, ticket.ticket_number, 'ms')">
                  <span v-if="!selectedInput || selectedInput !== 'ms' || selectedRow !== ticket.ticket_number">
                    {{ ticket.ms_no ? ticket.ms_no : 'N/A' }}
                  </span>
                  <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'ms'"
                    v-model="editData[ticket.ms_no]"
                    @blur="updateData(ticket.ms_no, ticket.ticket_number, 'ms_no', 'ms')"
                    @keyup.enter="updateData(ticket.ms_no, ticket.ticket_number, 'ms_no', 'ms')"
                    class="w-100 rounded border border-secondary-subtle text-center">
                </td>
                <td class="text-center" style="max-width: 60px;"
                  @click="showInput(ticket.rs_no, ticket.ticket_number, 'rs')">
                  <span v-if="!selectedInput || selectedInput !== 'rs' || selectedRow !== ticket.ticket_number">
                    {{ ticket.rs_no ? ticket.rs_no : 'N/A' }}
                  </span>
                  <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'rs'"
                    v-model="editData[ticket.rs_no]"
                    @blur="updateData(ticket.rs_no, ticket.ticket_number, 'rs_no', 'rs')"
                    @keyup.enter="updateData(ticket.rs_no, ticket.ticket_number, 'rs_no', 'rs')"
                    class="w-100 rounded border border-secondary-subtle text-center">
                </td>
                <td class="text-start text-truncate" style="max-width: 10rem;"
                  :title="ticket.employee.user.name + '\n' + ticket.employee.department + ' - ' + ticket.employee.office">
                  <span class="fw-medium">
                    {{ ticket.employee.user.name }}
                  </span>
                  <br>
                  <small>{{ ticket.employee.department }} - {{ ticket.employee.office }}</small>
                </td>
                <td class="text-start text-truncate ticket-description" style="max-width: 8rem"
                  data-hover-text="{{ ticket.issue }}{{ ticket.description }}">
                  <span :title="ticket.issue + '\n' + ticket.description">{{ ticket.issue }}</span>
                </td>
                <td class="text-center">
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
                    <button type="button" class="btn text-center">
                      {{ ticket.service ? ticket.service : 'N/A' }}
                    </button>
                  </div>
                </td>

                <td class="text-center">
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
                </td>
                <td class="text-start">
                  <div class="d-flex flex-column justify-content-center align-items-center">
                    <div v-for="(assignedTech, index) in ticket.assigned" :key="index">
                      <div class="btn-group position-static">
                        <button v-if="ticket.status !== 'Resolved'" type="button"
                          class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
                          aria-expanded="false" data-bs-reference="parent"
                          @click="fetchRecommended(ticket.employee.department)">
                          <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <div v-if="ticket.status !== 'Resolved'"
                          class="d-flex flex-row justify-content-center align-items-center"
                          v-for="(tech, techIndex) in assignedTech.technician" :key="techIndex">
                          <button type="button" class="btn text-start" style="width: 8rem;"
                            @click="toggleTechnicianCTAs">
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
                          <button type="button" class="btn text-start" style="width: 8rem;">
                            {{ tech.user.name }}
                          </button>
                        </div>
                        <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto; width: 8rem;">
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
                          <div>
                            <h6 class="dropdown-header">All</h6>
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
                        </ul>
                      </div>
                    </div>
                    <button v-if="technicianCTAs && ticket.status !== 'Resolved'"
                      class="btn align-items-center justify-content-center d-flex text-primary fs-5"
                      style="height:1.5em;" @click="addDropdown(ticket)">
                      <i class="bi bi-plus-circle-fill"></i>
                    </button>
                  </div>
                </td>
                <td class="text-center" style="max-width: 60px;"
                  @click="showInput(ticket.sr_no, ticket.ticket_number, 'sr', ticket.status)">
                  <span v-if="!selectedInput || selectedInput !== 'sr' || selectedRow !== ticket.ticket_number">
                    {{ ticket.sr_no ? ticket.sr_no : 'N/A' }}
                  </span>
                  <input type="text" v-if="selectedRow === ticket.ticket_number && selectedInput === 'sr'"
                    v-model="editData[ticket.sr_no]"
                    @blur="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no', 'sr')"
                    @keyup.enter="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no')"
                    class="w-100 rounded border border-secondary-subtle text-center">
                </td>
                <td class="text-start">
                  {{ isNaN(new Date(formatDate(ticket.resolved_at))) ? 'Not yet done' :
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
                  <div class="">
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
      <EmptyCard :title="'No tickets yet...'" v-else class="mt-2 w-75" style="height:20rem;">
      </EmptyCard>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import EmptyCard from '@/Components/EmptyState/Table.vue';
import Pagination from '@/Components/Pagination.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
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
  technicians: Object,
  filters: Object,
  services: Object,
  rr: Object,
  ms: Object,
  rs: Object,
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
    route('admin.tickets'),
    {
      search: search.value,
      from_date_filter: from_date_filter,
      to_date_filter: to_date_filter,
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
    // If newDate is not null or undefined, process it
    from_date_filter = moment(newDate[0]).format('YYYY-MM-DD');
    to_date_filter = moment(newDate[1]).format('YYYY-MM-DD');
  } else {
    // If newDate is null or undefined, set date filters to null
    from_date_filter = null;
    to_date_filter = null;
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

const showInput = (data, id, type, status) => {
  if (status != 'Resolved') {
    console.log(data);
    selectedInput.value = type;
    selectedRow.value = id;
    editData[data] = data ? data : '';
    console.log(selectedInput.value, editData[data], selectedRow.value);
  }
}

const updateData = async (data, id, updateField, type) => {
  console.log(editData[data])
  if (selectedInput.value !== type) {
    return;
  }

  if (!validateNumericInput(editData[data], updateField)) {
    return;
  }

  const formData = {
    [updateField]: editData[data],
    ticket_number: id,
  };

  console.log('start');
  try {
    // Assuming useForm and form.put return promises
    const form = useForm(formData);
    await form.put(route('admin.tickets.update', { ticket_id: id, field: updateField }));
    console.log('finished updating data');
  } catch (error) {
    console.error('Error updating data:', error);
  }

  selectedInput.value = null;
  editData[data] = '';
};

const removeData = async (data, id) => {
  console.log('start remove')
  try {
    const form = useForm({
      ticket_number: id,
      technician: data,
    })
    await form.delete(route('admin.tickets.remove.technician'))
  } catch (error) {
    console.error('Error removing data:', error)
  }
}

const replaceTechnician = async (id, technician, old) => {
  try {
    const form = useForm({
      ticket_number: id,
      technician: technician,
      old: old,
    })
    await form.put(route('admin.tickets.replace.technician'))
  } catch (error) {
    console.error('Error replacing data:', error);
  }
}

const addDropdown = (ticket) => {
  ticket.assigned.push({
    ticket_number: null,
    technician: [],
  })
  console.log(ticket.assigned)
}

const removeTechnician = async (ticket, assignedIndex, techId) => {
  console.log(techId)
  ticket.assigned[assignedIndex].technician.splice(techId, 1)
  removeData(techId, ticket.ticket_number)
}

const assignTechnician = async (ticket, assignedIndex, technician) => {
  // Check if there are already assigned technicians
  if (ticket.assigned[assignedIndex].technician && ticket.assigned[assignedIndex].technician.length > 0) {
    const old = ticket.assigned[assignedIndex].technician[0].technician_id
    await replaceTechnician(ticket.ticket_number, technician.technician_id, old)
  } else {
    // If there are no existing technicians, simply push the new technician
    ticket.assigned[assignedIndex].technician.splice(0, 1, technician);
    await showInput(ticket.assigned[assignedIndex].technician[0].technician_id, ticket.ticket_number, 'technician')
    await updateData(ticket.assigned[assignedIndex].technician[0].technician_id, ticket.ticket_number, 'technician', 'technician');
  }
};

let recommended = ref([]);

const fetchRecommended = async (department) => {
  try {
    const response = await axios.get(route('admin.recommended', department))

    if (response.status !== 200) {
      throw new Error('Failed to fetch recommended technicians');
    }

    recommended.value = response.data;

  } catch (error) {
    console.error('Error fetching recommended technicians:', error);
    return null;
  }
}

// Table update end

// Styling and formatting
let technicianCTAs = ref(false);

// Function to toggle the visibility of CTAs for a specific row
const toggleTechnicianCTAs = () => {
  // Toggle the visibility of CTAs for the clicked row
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
    font-size: 16px;
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
    font-size: 16px;
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
    font-size: 16px;
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
    font-size: 16px;

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