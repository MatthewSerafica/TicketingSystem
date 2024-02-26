<template>
  <div>
    <Header></Header>
    <form @submit.prevent="create">
      <div class="form-container1">
        <h2>SERVICE REPORT FORM</h2>
        {{ service_report }}
        <div class="placeholder-text">{{ nextTicketNumber || 'SR-0001' }}</div>

        <div class="row-input-container">
          <div class="input-container">
            <label for="dateStarted">Date Started:</label>
            <input type="date" id="dateStarted" v-model="form.date_started">
          </div>

          <div class="input-container">
            <label for="timeStarted">Time Started:</label>
            <input type="time" id="timeStarted" v-model="form.time_started">
          </div>

          <div class="input-container">
            <label for="ticketNumber">Ticket Number:</label>
            <input type="text" id="ticketNumber" v-model="form.ticket_number">
          </div>
        </div>

        <div class="input-container">
          <label for="technicianName">Technician Name:</label>
          <input type="text" id="technicianName" v-model="form.technician_name" class="long-input1">

          <label for="requestingOffice">Requesting Office:</label>
          <input type="text" id="requestingOffice" v-model="form.requesting_office" class="long-input1">

          <label for="equipment_no">Equipment, Property Tag/Serial No.:</label>
          <input type="text" id="equipment_no" v-model="form.equipment_no" class="long-input1">

          <label for="problemEncountered">Problem Encountered:</label>
          <select id="problemEncountered" v-model="form.issue" class="long-input1 custom-select">
            <option value="">Select an option</option>
            <option value="No Internet">No Internet</option>
            <option value="Software Installation">Software Installation</option>
            <option value="Printer Problem">Printer Problem</option>
          </select>

          <label for="actionTaken">Action Taken:</label>
          <input type="text" id="action" v-model="form.action_taken" class="long-input1">

          <label for="recommendation">Recommendation:</label>
          <input type="text" id="recommendation" v-model="form.recommendation" class="long-input1">
        </div>

        <div class="row-input-container">
          <div class="input-container">
            <label for="dateDone">Date Done:</label>
            <input type="date" id="dateDone" v-model="form.date_done" class="long-input2" :min="minDate">
          </div>

          <div class="input-container">
            <label for="timeDone">Time Done:</label>
            <input type="time" id="timeDone" v-model="form.time_done" class="long-input2">
          </div>
        </div>

        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-6">
              <div class="d-flex justify-content-center gap-2">
                <Button :name="'Submit'" :color="'primary'" @click="submitForm"></Button>
                <Link :href=" '/technician/service-report'"  class="btn btn-outline-primary">Cancel</Link>
              </div>
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
  next_service_number: Object,
  service_report: Object,
})

const today = new Date();
const minDate = today.toISOString().split('T')[0]; 

const page = usePage();

const form = useForm({
  service_id: props.nextTicketNumber,
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


const create = () => form.post(route('technician.service-report.store'), { preserveScroll: false, preserveState: false })
</script>

<style scoped>
.form-container1 {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 50px; 
  border-radius: 10px; 
  background-color: white; 
  padding: 20px; 
}

.form-container1 h2 {
  margin-bottom: 15px; 
}

.row-input-container {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  margin-bottom: 15px; 
}

.input-container {
  margin-top: 15px;
  margin-right: 10px;
  margin-bottom: 15px;
}

.custom-select {
  height: 43px; 
}

.input-container label {
  display: block;
}

.long-input1 {
  width: 620px; 
}

.long-input2 {
  width: 305px; 
}

input, select {
  border: 1px solid #ccc; 
  border-radius: 5px;
  padding: 8px;
  width: 200px; 
}

.cancel-button {
  background-color: #000066;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button {
  width: 200%;
  background-color: #000066;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  margin-right: 10px;
  transition: background-color 0.3s;
}

.cancel-button:hover,
.submit-button:hover {
  background-color: #0c0c36; 
}
</style>
