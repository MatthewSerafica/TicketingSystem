<template>
  <div>
    <Header></Header>
    <form>
      <div class="form-container1">
        <h2>SERVICE REPORT FORM</h2>

        <!-- Group for Date Started, Time Started, Ticket Number -->
        <div class="row-input-container">
          <!-- Date Started -->
          <div class="input-container">
            <label for="dateStarted">Date Started:</label>
            <input type="date" id="dateStarted" v-model="form.dateStarted">
          </div>

          <!-- Time Started -->
          <div class="input-container">
            <label for="timeStarted">Time Started:</label>
            <input type="time" id="timeStarted" v-model="form.timeStarted">
          </div>

          <!-- Ticket Number -->
          <div class="input-container">
            <label for="ticketNumber">Ticket Number:</label>
            <input type="text" id="ticketNumber" v-model="form.ticketNumber">
          </div>
        </div>

        <!-- Group for Technician Name, Requesting Office, Equipment, Property Tag/Serial No., Action Taken, Recommendation -->
        <div class="input-container">
          <!-- Technician Name -->
          <label for="technicianName">Technician Name:</label>
          <input type="text" id="technicianName" v-model="form.technicianName" class="long-input1">

          <!-- Requesting Office -->
          <label for="requestingOffice">Requesting Office:</label>
          <input type="text" id="requestingOffice" v-model="form.requestingOffice" class="long-input1">

          <!-- Equipment, Property Tag/Serial No. -->
          <label for="equipmentNumber">Equipment, Property Tag/Serial No.:</label>
          <input type="text" id="equipmentNumber" v-model="form.equipmentNumber" class="long-input1">

          <!-- Problem Encountered -->
          <label for="problemEncountered">Problem Encountered:</label>
          <select id="problemEncountered" v-model="form.problemEncountered" class="long-input1 custom-select">
            <option value="">Select an option</option>
            <option value="No Internet">No Internet</option>
            <option value="Computer Not Working">Computer Not Working</option>
          </select>

          <!-- Action Taken -->
          <label for="actionTaken">Action Taken:</label>
          <input type="text" id="actionTaken" v-model="form.actionTaken" class="long-input1">

          <!-- Recommendation -->
          <label for="recommendation">Recommendation:</label>
          <input type="text" id="recommendation" v-model="form.recommendation" class="long-input1">
        </div>

        <!-- Group for Date Done, Time Done -->
        <div class="row-input-container">
          <!-- Date Done -->
          <div class="input-container">
            <label for="dateDone">Date Done:</label>
            <input type="date" id="dateDone" v-model="form.dateDone" class="long-input2" :min="minDate">
          </div>

          <!-- Time Done -->
          <div class="input-container">
            <label for="timeDone">Time Done:</label>
            <input type="time" id="timeDone" v-model="form.timeDone" class="long-input2">
          </div>
        </div>

        <!-- Submit Button -->
        <div class="input-container">
          <Link href="/technician/service-report">
            <button type="button" class="cancel-button">Cancel</button>
          </Link>
            <button type="button" @click="submitForm" class="submit-button">Submit</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import Header from '@/Pages/Layouts/TechnicianHeader.vue'
import { Link, router, useForm } from '@inertiajs/vue3';

const today = new Date();
const minDate = today.toISOString().split('T')[0]; 

const form = useForm({
  dateStarted: '',
  timeStarted: '',
  ticketNumber: '',
  technicianName: '',
  requestingOffice: '',
  equipmentNumber: '',
  problemEncountered: '',
  actionTaken: '',
  recommendation: '',
  dateDone: '',
  timeDone: ''
});


const submitForm = () => {
  console.log('Form submitted:', form);
};
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
  margin-right: 10px; /* Add margin to create space */
  transition: background-color 0.3s;
}

.submit-button {
  background-color: #000066;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cancel-button:hover,
.submit-button:hover {
  background-color: #0c0c36; 
}
</style>
