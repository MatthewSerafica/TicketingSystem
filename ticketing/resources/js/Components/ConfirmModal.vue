<template>
  <div class="custom-modal" @close="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Submission</h5>
        <button type="button" class="btn-close" aria-label="Close" @click="closeModal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to submit this service report?</p>
        <ul>
          <li><strong>Ticket Number:</strong> {{ form.ticket_number || 'N/A'  }}</li>
          <li><strong>Date Started:</strong> {{ form.date_started || 'N/A'  }}</li>
          <li><strong>Time Started:</strong> {{ form.time_started || 'N/A'  }}</li>
          <li><strong>Technician Name:</strong> {{ form.technician || 'N/A'  }}</li>
          <li><strong>Requesting Office:</strong> {{ form.requesting_office || 'N/A'  }}</li>
          <li><strong>Equipment, Property Tag/Serial No.:</strong> {{ form.equipment_no || 'N/A'  }}</li>
          <li><strong>Problem Encountered:</strong> {{ form.issue || 'N/A'  }}</li>
          <li><strong>Action Taken:</strong> {{ form.action || 'N/A'  }}</li>
          <li><strong>Recommendation:</strong> {{ form.recommendation || 'N/A'  }}</li>
          <li><strong>Date Done:</strong> {{ form.date_done || 'N/A'  }}</li>
          <li><strong>Time Done:</strong> {{ form.time_done || 'N/A'  }}</li>
          <li><strong>Remarks:</strong> {{ form.remarks || 'N/A'  }}</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
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

const closeModal = () => {
  emit('closeModal');
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
