<template>
  <div class="custom-modal" @close="closeSubmitService">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Submission</h5>
        <button type="button" class="btn-close" aria-label="Close" @click="closeSubmitService"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to submit this service report?</p>
        <ul>
          <li><strong>Service ID:</strong> {{ form.service_id }}</li>
          <li><strong>Date Started:</strong> {{ form.date_started }}</li>
          <li><strong>Time Started:</strong> {{ form.time_started }}</li>
          <li><strong>Ticket Number:</strong> {{ form.ticket_number }}</li>
          <li><strong>Technician Name:</strong> {{ form.technician }}</li>
          <li><strong>Requesting Office:</strong> {{ form.requesting_office }}</li>
          <li><strong>Equipment, Property Tag/Serial No.:</strong> {{ form.equipment_no }}</li>
          <li><strong>Problem Encountered:</strong> {{ form.problem }}</li>
          <li><strong>Action Taken:</strong> {{ form.action }}</li>
          <li><strong>Recommendation:</strong> {{ form.recommendation }}</li>
          <li><strong>Date Done:</strong> {{ form.date_done }}</li>
          <li><strong>Time Done:</strong> {{ form.time_done }}</li>
          <li><strong>Remarks:</strong> {{ form.remarks }}</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" @click="closeSubmitService">Cancel</button>
        <button type="button" class="btn btn-primary" @click="submitServiceReport">Confirm Submission</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, ref,  defineEmits } from 'vue';

const props = defineProps({
  form: Object, // Pass the form object to the modal
});

const emit = defineEmits(['closeSubmitService'])

const closeSubmitService = () => {
  emit('closeSubmitService');
  console.log("Close Button clicked");
};

const submitServiceReport = () => {
  // Implement your logic to submit the service report here
  console.log('Props in ConfirmModal.vue:', props);

  form.post(route('technician.service-report.create'), { preserveScroll: false, preserveState: false });
};



</script>

<style scoped>
.custom-modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  text-align: center;
  width: 25%;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
}
</style>
