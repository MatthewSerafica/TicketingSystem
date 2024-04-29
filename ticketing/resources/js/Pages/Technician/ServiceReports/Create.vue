<template>
  <div>
    <Header></Header>
    <div class="mt-2 pt-5">
      <br />
      <div class="container">
        <div class="title-container text-center">
          <h1 class="fw-bold">Create Service Reports</h1>
        </div>

        <div class="create-report mb-3">
          <div class="row justify-content-center mb-4">
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text">SR-A:</span>
                <input type="text" class="form-control" id="service_id" v-model="form.service_id" required>
              </div>
              <span v-if="form.errors.service_id" class="error-message">{{ form.errors.service_id }}</span>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-4">
              <label for="dateStarted" class="form-label">Date Started:</label>
              <input type="date" class="form-control" id="dateStarted" v-model="form.date_started" required>
            </div>
            <div class="col-md-4">
              <label for="timeStarted" class="form-label">Time Started:</label>
              <input type="time" class="form-control" id="timeStarted" v-model="form.time_started" required>
            </div>

            <div class="col-md-4">
              <label for="ticketNumber" class="form-label">Ticket Number:</label>
              <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  {{ form.ticket_number ? form.ticket_number : 'Select a ticket number...' }}
                </button>
                <ul class="dropdown-menu">
                  <li class="dropdown-item">
                    Select a Ticket No.
                  </li>
                  <li v-for="ticket in tickets">
                    <div class="dropdown-item" @click="ticketDetails(ticket)">{{ ticket.ticket_number }}</div>
                  </li>
                </ul>
              </div>

              <span v-if="form.errors.ticket_number" class="error-message">{{ form.errors.ticket_number }}</span>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-4">
              <label for="technicianName" class="form-label">Technician Name:</label>
              <textarea class="form-control" id="technicianName" v-model="form.technician" readonly> </textarea>
            </div>
            <div class="col-md-4">
              <label for="requestingOffice" class="form-label">Requesting Office:</label>
              <input type="text" class="form-control" id="requestingOffice" v-model="form.requesting_office">
            </div>
            <div class="col-md-4">
              <label for="equipment_no" class="form-label">Equipment, Property Tag/Serial No.:</label>
              <input type="text" class="form-control" id="equipment_no" v-model="form.equipment_no">
            </div>
          </div>


          <div class="row mb-4">
            <div class="col-md-4">
              <label for="problemEncountered" class="form-label">Problem Encountered:</label>
              <input type="text" class="form-control" id="problemEncountered" v-model="form.problem">
            </div>
            <div class="col-md-4">
              <label for="action" class="form-label">Action Taken:</label>
              <input type="text" class="form-control" id="action" v-model="form.action">
            </div>
            <div class="col-md-4">
              <label for="recommendation" class="form-label">Recommendation:</label>
              <input type="text" class="form-control" id="recommendation" v-model="form.recommendation">
            </div>
          </div>


          <div class="row mb-4">
            <div class="col-md-4">
              <label for="dateDone" class="form-label">Date Done:</label>
              <input type="date" class="form-control" id="dateDone" v-model="form.date_done" :min="minDate">
            </div>
            <div class="col-md-4">
              <label for="timeDone" class="form-label">Time Done:</label>
              <input type="time" class="form-control" id="timeDone" v-model="form.time_done">
            </div>
            <div class="col-md-4">
              <label for="remarks" class="form-label">Remarks</label>
              <textarea class="form-control" id="remarks" v-model="form.remarks"></textarea>
            </div>
          </div>

          <ConfirmModal v-if="showConfirmationModal" :form="selectedServiceReport" :id="form.technicianId"
            :ticket="selectedTicket" @confirm="create" @closeSubmitService="closeSubmitService" />
          <!--  {{ selectedTicket }} -->
          <div class="row justify-content-end">
            <div class="col-md-4">
              <div class="d-flex justify-content-end gap-2">
                <Button :name="'Submit'" :color="'primary'" @click="submitServiceReport(form, selectedTicket)"
                  :disabled="!form.service_id || !form.date_started || !form.time_started || !form.ticket_number || !form.requesting_office || !form.equipment_no || !form.problem || !form.action || !form.recommendation || !form.date_done || !form.time_done"></Button>
                <Button :name="'Cancel'" :color="'light'" @click="back(props.new_service_id)"
                  class="btn btn-outline-secondary"></Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import Header from '@/Pages/Layouts/TechnicianHeader.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from "vue";

const props = defineProps({
  technicians: Object,
  new_service_id: String,
  ticket_id: Number,
  service_report: Object,
  tickets: Object,
  date_done: String,
})

const today = new Date();
const minDate = today.toISOString().split('T')[0];

const page = usePage();

const form = useForm({
  service_id: props.new_service_id,
  date_started: null,
  time_started: null,
  ticket_number: props.ticket_id,
  technician: [],
  technicianId: [],
  requesting_office: null,
  equipment_no: null,
  problem: null,
  action: null,
  recommendation: null,
  date_done: props.date_done,
  time_done: null,
  remarks: null,
});

const fillFormWithTicket = (ticket) => {
  form.action = ticket.service;
  form.problem = ticket.issue;
  form.requesting_office = `${ticket.employee.department} - ${ticket.employee.office}`;
  // Fill other form fields as needed
};

let selectedTicket = ref([]);

onMounted(async () => {
  // Check if props.ticket_id is present
  if (props.ticket_id) {
    const ticketSelected = props.tickets.find(ticket => ticket.ticket_number === props.ticket_id);
    selectedTicket = ticketSelected;
    if (ticketSelected) {
      fillFormWithTicket(ticketSelected);
      const response = await fetch(route('technician.tickets.assigned', { id: ticketSelected.ticket_number }));
      const data = await response.json();
      const technicianNames = [];
      const technicianId = [];

      for (let i = 0; i < data.length; i++) {
        technicianNames.push(data[i].technician[0].user.name);
        technicianId.push(data[i].technician[0].technician_id);
      }
      const formattedTechnicianNames = technicianNames.join(' / ');
      form.technician = formattedTechnicianNames;
      form.technicianId = technicianId;
      console.log(form.technician);
    }
  }


});

const create = () => {
  if (!/^\d+$/.test(form.ticket_number)) {
    form.errors.ticket_number = 'Ticket number should contain only numeric characters.';
    return;
  }
  form.post(route('technician.service-report.store'), { preserveScroll: false, preserveState: false });
}

watch(() => form.ticket_number, async (newValue) => {
  const selectedTicket = props.tickets.find(ticket => ticket.ticket_number === newValue);

  if (selectedTicket) {
    form.action = selectedTicket.service;
    form.problem = selectedTicket.issue;
    form.requesting_office = selectedTicket.employee.department + ' - ' + selectedTicket.employee.office;
    const response = await fetch(route('technician.tickets.assigned', { id: selectedTicket.ticket_number }));
    const data = await response.json();
    const technicianNames = [];
    const technicianId = [];
    for (let i = 0; i < data.length; i++) {
      technicianNames.push(data[i].technician[0].user.name);
      technicianId.push(data[i].technician[0].technician_id);
    }
    const formattedTechnicianNames = technicianNames.join(' / ');
    form.technician = formattedTechnicianNames;
    form.technicianId = technicianId;
    console.log(form.technician);
  }
})

let selectedServiceReport = ref(null);
const showConfirmationModal = ref(false);

const ticketDetails = (ticket) => {
  if (ticket) {
    selectedTicket = null;
    form.ticket_number = ticket.ticket_number;
    selectedTicket = ticket;
    console.log('Selected Ticket ID:', ticket.ticket_number);
  }
}

const submitServiceReport = (sr, ticket) => {
  if (!showConfirmationModal.value) {
    console.log('Form data in Create.vue:', form);
    console.log('Selected service report:', sr);
    console.log('showConfirmationModal before opening:', showConfirmationModal.value);
    selectedServiceReport.value = sr;
    showConfirmationModal.value = true;
    console.log('showConfirmationModal after opening:', showConfirmationModal.value);
  }
};


function closeSubmitService() {
  if (showConfirmationModal.value) {
    selectedServiceReport.value = null
    showConfirmationModal.value = false;
  }
}

const back = async (id) => {
  try {
    console.log("cancel button clicked");
    /* router.post({ name: 'technician.service-reports.back', params: { service_id: props.new_service_id } }); */
    router.post(route('technician.service-reports.back', id));
    console.log("cancel routing");
  } catch (err) {
    console.error(err);
  }
}
</script>

<style scoped>
.error-message {
  color: red;
}

.input-group {
  width: 100%;
}

.form-control {
  width: 100%;
}
</style>
