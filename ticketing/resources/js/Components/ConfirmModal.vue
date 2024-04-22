<template>
  <div class="custom-modal" @close="closeSubmitService">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title  text-center">Are you sure you want to submit this service report?</h5>
        <button type="button" class="btn-close ms-auto" aria-label="Close" @click="closeSubmitService"></button>

      </div>
      <div class="modal-body">
        <div>
          <div class="card">
            <div class="card-body">
              <p class="modal-title  text-center"><strong>Ticket Details:</strong></p>
              <p><strong>Ticket Number:</strong> {{ ticket.ticket_number }}</p>
              <p><strong>Date Created:</strong> {{ ticket.date_created }}</p>
              <p><strong>Request Type:</strong> {{ ticket.request_type }}</p>
              <p><strong>Complexity:</strong> {{ ticket.complexity }}</p>
              <p><strong>RR No:</strong> {{ ticket.rr_no }}</p>
              <p><strong>MS No:</strong> {{ ticket.ms_no }}</p>
              <p><strong>RS No:</strong> {{ ticket.rs_no }}</p>
              <p><strong>Issue:</strong> {{ ticket.issue }}</p>
              <p><strong>Description:</strong> {{ ticket.description }}</p>
              <p><strong>Service:</strong> {{ ticket.service }}</p>
            </div>
          </div>
        </div>
        <div v-if="form">
          <div class="card">
            <div class="card-body">
              <p class="modal-title  text-center"><strong>Service Report Details:</strong></p>
              <p><strong>Service ID:</strong> {{ form.service_id }}</p>
              <p><strong>Date Started:</strong> {{ form.date_started }}</p>
              <p><strong>Time Started:</strong> {{ form.time_started }}</p>
              <p><strong>Ticket Number:</strong> {{ form.ticket_number }}</p>
              <p><strong>Technician Name:</strong> {{ form.technician }}</p>
              <p><strong>Requesting Office:</strong> {{ form.requesting_office }}</p>
              <p><strong>Equipment, Property Tag/Serial No.:</strong> {{ form.equipment_no }}</p>
              <p><strong>Problem Encountered:</strong> {{ form.problem }}</p>
              <p><strong>Action Taken:</strong> {{ form.action }}</p>
              <p><strong>Recommendation:</strong> {{ form.recommendation }}</p>
              <p><strong>Date Done:</strong> {{ form.date_done }}</p>
              <p><strong>Time Done:</strong> {{ form.time_done }}</p>
              <p><strong>Remarks:</strong> {{ form.remarks }}</p>
            </div>
          </div>
        </div>
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
  ticket: Object,
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
  text-align: left;
  width: 80%;
}

.modal-title {
  border-radius: 8px;
  text-align: left;
  width: 100%;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
}
</style>
