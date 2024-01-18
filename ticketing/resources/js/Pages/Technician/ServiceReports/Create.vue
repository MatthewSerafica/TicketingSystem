<template>
  <div>
    <Header></Header>
    <form>
      <div class="form-container1">
        <h2>SERVICE REPORT FORM</h2>

        <!-- Group for Date Started, Time Started, Ticket Number -->
        <div class="row-input-container1">
          <!-- ... -->
        </div>

        <!-- Common container for both row-input-container1 and row-input-container2 -->
        <div class="row-container">
          <!-- Group for Technician Name, Requesting Office, Equipment, Property Tag/Serial No. -->
          <div class="row-input-container2">
            <!-- ... -->
          </div>
        </div>

        <!-- Group for Problem Encountered, Action Taken, Recommendation -->
        <div class="row-input-container">
          <!-- ... -->
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import Header from '@/Pages/Layouts/TechnicianHeader.vue'
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  dateStarted: '',
  timeStarted: '',
  ticketNumber: '',
  technicianName: '',
  requestingOffice: '',
  equipmentNumber: '',
  problemEncountered: '',
  actionTaken: '',
  recommendation: ''
});
</script>

<style scoped>
.form-container1 {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 50px; /* Adjust as needed */
  border-radius: 10px; /* Add border-radius for rounded corners */
  background-color: white; /* Add a background color for better visibility */
  padding: 20px; /* Add padding for spacing inside the container */
}

.form-container1 h2 {
  margin-bottom: 15px; /* Adjust as needed for spacing between header and labels */
}

.row-container {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
}

.row-input-container1,
.row-input-container2 {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  margin-bottom: 15px; /* Add margin-bottom for space between rows */
}

.input-container {
  margin-top: 15px; /* Adjust as needed for spacing between inputs */
  margin-right: 10px; /* Add margin-right for space between individual input containers */
}

.input-container label {
  display: block; /* Make labels block-level for better styling */
}

/* Add border-radius to the input elements */
input {
  border: 1px solid #ccc; /* Add a border for better visibility */
  border-radius: 5px; /* Add border-radius for rounded corners */
  padding: 8px; /* Add padding for spacing inside the input */
  width: 200px; /* Adjust width as needed */
}
</style>
