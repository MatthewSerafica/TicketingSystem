<template>
  <div>
    <Header></Header>
    <form @submit.prevent="create" class="container">
      <div class="form-container1 d-flex justify-content-center align-items-center flex-column">
        <h2 class="text-center pt-5">SERVICE REPORT FORM</h2>

        <div class="col-md-6">
        <div class="input-group mb-3">
          <span class="input-group-text">SR-A:</span>
          <input type="text" class="form-control" id="service_id" v-model="form.service_id" required>
          <span v-if="form.errors.service_id" class="error-message">{{ form.errors.service_id }}</span>
          </div>
        </div>

        <div class="col-md-6">
        <div class="row mb-3">
          <div class="col">
            <label for="dateStarted" class="form-label">Date Started:</label>
            <input type="date" class="form-control" id="dateStarted" v-model="form.date_started" required>
          </div>

          <div class="col">
            <label for="timeStarted" class="form-label">Time Started:</label>
            <input type="time" class="form-control" id="timeStarted" v-model="form.time_started" required>
          </div>

          <div class="col">
            <label for="ticketNumber" class="form-label">Ticket Number:</label>
            <input type="text" class="form-control" id="ticketNumber" v-model="form.ticket_number" required>
          </div>
        </div>
        </div>

        
        <div class="col-md-6">
          <label for="technicianName" class="form-label">Technician Name:</label>
          <input type="text" class="form-control" id="technicianName" v-model="form.technician_name">
  
          <label for="requestingOffice" class="form-label">Requesting Office:</label>
          <input type="text" class="form-control" id="requestingOffice" v-model="form.requesting_office">
  
          <label for="equipment_no" class="form-label">Equipment, Property Tag/Serial No.:</label>
          <input type="text" class="form-control" id="equipment_no" v-model="form.equipment_no">
          
          <div class="row mb-3">
            <div class="col">
              <label for="problemEncountered" class="form-label">Problem Encountered:</label>
              <select id="problemEncountered" v-model="form.issue" class="form-control long-input1 custom-select">
                <option value="">Select an option</option>
                <option value="No Internet">No Internet</option>
                <option value="Software Installation">Software Installation</option>
            <option value="Printer Problem">Printer Problem</option>
          </select>
          </div>
          
          <div class="col-md-6">
          <label for="action" class="form-label">Action Taken:</label>
          <input type="text" class="form-control" id="action" v-model="form.action">
          </div>
          </div>

          <label for="recommendation" class="form-label">Recommendation:</label>
          <input type="text" class="form-control" id="recommendation" v-model="form.recommendation">
        </div>

        <div class="col-md-6">
        <div class="row mb-3">
          <div class="col">
            <label for="dateDone" class="form-label">Date Done:</label>
            <input type="date" class="form-control" id="dateDone" v-model="form.date_done" :min="minDate">
          </div>

          <div class="col">
            <label for="timeDone" class="form-label">Time Done:</label>
            <input type="time" class="form-control" id="timeDone" v-model="form.time_done">
          </div>
        </div>
        </div>
        
        <div class="col-md-6">
        <div class="mb-3">
          <label for="remarks" class="form-label">Remarks</label>
          <input type="text" class="form-control" id="remarks" v-model="remarks">
        </div>
        </div>

        <div class="row justify-content-end">
          <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2">
              <Button :name="'Submit'" :color="'primary'" ></Button>
              <Link :href="'/technician/service-report'" class="btn btn-outline-primary">Cancel</Link>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import Header from '@/Pages/Layouts/TechnicianHeader.vue'
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue'

const props = defineProps({
  technicians: Object,
  new_service_id: String,
  service_report: Object,
})

const today = new Date();
const minDate = today.toISOString().split('T')[0]; 

const page = usePage();

const form = useForm({
  service_id: props.new_service_id,
  date_started: '',
  time_started: '',
  ticket_number: '',
  technician_name: page.props.user.name,
  requesting_office: '',
  equipment_no: '',
  issue: '',
  action: '',
  recommendation: '',
  date_done: '',
  time_done: '',
  remarks:''
});


/* const create = () => {
    form.post(route('technician.service-report.store'), { preserveScroll: false, preserveState: false });
} */



const create = async () => {
    const is_validserviceid = await validate_service_id(form.service_id);

    if (!is_validserviceid) {
        form.errors.service_id = 'Invalid or duplicate service id';
        return;
    }

    form.post(route('technician.service-report.store'), { preserveScroll: false, preserveState: false });
}

const validate_service_id = async (service_id) => {
    const service_id_regex = /^SR-B \d{4}$/;

    if (!service_id_regex.test(service_id)) {
        console.error('Invalid service_id format');
        return false;
    }

    const response = await fetch(`/check-service-id/${service_id}`);
    const data = await response.json();

    if (data.exists) {
        console.error('Service id already exists');
        return false;
    }

    return true;
}
</script>

<style scoped>
.form-container1 {
  border-radius: 10px; 
  background-color: white; 
  padding: 20px; 
}

.form-container1 h2 {
  margin-bottom: 15px; 
}

.error-message {
  color: red;
}



</style>
