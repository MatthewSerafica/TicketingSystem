<template>
  <div>
    <Header></Header>
    <div class="mt-2 pt-5">
      <form @submit.prevent="create">
        <br />
        <div class="container">
          <div class="title-container text-center">
            <h1>SERVICE REPORT FORM</h1>
          </div>
          <div class="create-report">
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
                <select class="form-select" id="ticketNumber" v-model="form.ticket_number" required>
                  <option disabled>Assign ticket</option>
                  <option v-for="ticket in tickets" :value="ticket.ticket_number">{{ ticket.ticket_number }}</option>
                </select>
              </div>
            </div>
            <div class="row mb-4">
              <div class="col-md-4">
                <label for="technician" class="form-label">Technicians:</label>
                <textarea class="form-control" id="technician" v-model="form.technician"></textarea>
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
                <input type="text" id="problemEncountered" v-model="form.issue" class="form-control">
              </div>
              <div class="col-md-4">
                <label for="action" class="form-label">Action Taken:</label>
                <select class="form-select" id="action" v-model="form.action">
                  <option disabled>Assign action taken</option>
                  <option v-for="service in services" :value="service.service">{{ service.service }}</option>
                </select>
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
                <textarea type="text" class="form-control" id="remarks" v-model="form.remarks"></textarea>
              </div>
            </div>
            <div class="row justify-content-end">
              <div class="col-md-4">
                <div class="d-flex justify-content-end gap-2">
                  <Button :name="'Submit'" :color="'primary'"></Button>
                  <Link :href="'/admin/reports/service-report'" class="btn btn-outline-primary">Cancel</Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from "vue";

const props = defineProps({
  technicians: Object,
  new_service_id: String,
  service_report: Object,
  services: Object,
  tickets: Object,
})

const today = new Date();
const minDate = today.toISOString().split('T')[0];

const form = useForm({
  service_id: props.new_service_id,
  date_started: null,
  time_started: null,
  ticket_number: null,
  technician: [],
  requesting_office: null,
  equipment_no: null,
  issue: null,
  action: null,
  recommendation: null,
  date_done: null,
  time_done: null,
  remarks: null,
});


const create = async () => {
  if (!/^\d+$/.test(form.ticket_number)) {
    form.errors.ticket_number = 'Ticket number should contain only numeric characters.';
    return;
  }

  const is_valid_service_id = await validate_service_id(form.service_id);

  if (!is_valid_service_id) {
    form.errors.service_id = 'Invalid or duplicate service ID';
    return;
  }

  form.post(route('admin.reports.service-report.store'), { preserveScroll: false, preserveState: false });
}



const validate_service_id = async (service_id) => {
  const service_id_regex = /^\d{4}$/;

  if (!service_id_regex.test(service_id)) {
    console.error('Invalid service_id format');
    return false;
  }

  const response = await fetch(`/admin/check-service-id/${service_id}`);
  const data = await response.json();

  if (data.exists) {
    console.error('Service id already exists');
    return false;
  }

  return true;
}

watch(() => form.ticket_number, async (newValue) => {
  const selectedTicket = props.tickets.find(ticket => ticket.ticket_number === newValue);

  console.log(selectedTicket.ticket_number)
  if (selectedTicket) {
    form.action = selectedTicket.service;
    form.issue = selectedTicket.description;
    form.requesting_office = selectedTicket.employee.department + ' - ' + selectedTicket.employee.office;

    const response = await fetch(route('admin.tickets.assigned', { id: selectedTicket.ticket_number }));
    const data = await response.json();
    const technicianNames = [];
    const technicianId = [];

    for (let i = 0; i < data.length; i++) {
      technicianNames.push(data[i].technician[0].user.name);
      technicianId.push(data[i].technician[0].technician_id);
    }
    
    const formattedTechnicianNames = technicianNames.join(' / ');
    form.technician = formattedTechnicianNames;
  }
})

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
