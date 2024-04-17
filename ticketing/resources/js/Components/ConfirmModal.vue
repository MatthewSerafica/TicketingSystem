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
          <li><strong>Ticket Number:</strong> {{ props.form.ticket_number || 'N/A'  }}</li>
          <li><strong>Date Started:</strong> {{ props.form.date_started || 'N/A'  }}</li>
          <li><strong>Time Started:</strong> {{ props.form.time_started || 'N/A'  }}</li>
          <li><strong>Technician Name:</strong> {{ props.form.technician || 'N/A'  }}</li>
          <li><strong>Requesting Office:</strong> {{ props.form.requesting_office || 'N/A'  }}</li>
          <li><strong>Equipment, Property Tag/Serial No.:</strong> {{ props.form.equipment_no || 'N/A'  }}</li>
          <li><strong>Problem Encountered:</strong> {{ props.form.issue || 'N/A'  }}</li>
          <li><strong>Action Taken:</strong> {{ props.form.action || 'N/A'  }}</li>
          <li><strong>Recommendation:</strong> {{ props.form.recommendation || 'N/A'  }}</li>
          <li><strong>Date Done:</strong> {{ props.form.date_done || 'N/A'  }}</li>
          <li><strong>Time Done:</strong> {{ props.form.time_done || 'N/A'  }}</li>
          <li><strong>Remarks:</strong> {{ props.form.remarks || 'N/A'  }}</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" as="button"  class="btn btn-outline-secondary " @click="closeSubmitService">Cancel</button>
        <button type="button" class="btn btn-primary" @click="submitServiceReport">Confirm Submission</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
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
