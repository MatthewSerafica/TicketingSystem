<template>
  <div class="custom-modal" @close="closeSubmitService">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title  text-center">Are you sure you want to submit this service report?</h5>
      </div>
      <div class="modal-body d-flex gap-3 justify-content-center">
        <div>
          <div class="card">
            <div class="card-body">
              <p class="card-title  text-center"><strong>Ticket Details:</strong></p>
              <div class="text">
              <p class="card-text"><strong>Ticket Number:</strong> {{   ticket.ticket_number }}</p>
              <p class="card-text"><strong>Date Created:</strong> {{ formattedDate(ticket.created_at) }}</p>
              <p class="card-text"><strong>Request Type:</strong> {{ ticket.request_type ?? "Not available"}}</p>
              <p class="card-text"><strong>Complexity:</strong> {{ ticket.complexity ?? "Not available"}}</p>
              <p class="card-text"><strong>RR No:</strong> {{ ticket.rr_no ?? "Not available"}}</p>
              <p class="card-text"><strong>MS No:</strong> {{ ticket.ms_no ?? "Not available"}}</p>
              <p class="card-text"><strong>RS No:</strong> {{ ticket.rs_no ?? "Not available"}}</p>
              <p class="card-text"><strong>Issue:</strong> {{ ticket.issue ?? "Not available"}}</p>
              <p class="card-text"><strong>Description:</strong> {{ ticket.description ?? "Not available"}}</p>
              <p class="card-text"><strong>Service:</strong> {{ ticket.service ?? "Not available"}}</p>
              </div>
            </div>
          </div>
        </div>
        <div v-if="form">
          <div class="card">
            <div class="card-body row">
                <div class="col-md-6">
                  <p class="modal-title text-center"><strong>Service Report Details:</strong></p>
                  <div class="text">
                    {{ id }}
                  <p class="card-text"><strong>Service ID:</strong> {{ form.service_id ?? "Not available"}}</p>
                  <p class="card-text"><strong>Date Started:</strong> {{ formattedDate(form.date_started) ?? "Not available"}}</p>
                  <p class="card-text"><strong>Time Started:</strong> {{ form.time_started ?? "Not available"}}</p>
                  <p class="card-text"><strong>Ticket Number:</strong> {{ form.ticket_number ?? "Not available"}}</p>
                  <p class="card-text"><strong>Technician Name:</strong> {{ form.technician ?? "Not available"}}</p>
                  <p class="card-text"><strong>Requesting Office:</strong> {{ form.requesting_office ?? "Not available"}}</p>
                  <p class="card-text"><strong>Equipment, Property Tag/Serial No.:</strong> {{ form.equipment_no ?? "Not available"}}</p>
                </div>
              </div>

              <!-- Second Column -->
              <div class="col-md-6">
                <div class="text">
                <p class="card-text"><strong>Problem Encountered:</strong> {{ form.problem ?? "Not available"}}</p>
                <p class="card-text"><strong>Action Taken:</strong> {{ form.action ?? "Not available"}}</p>
                <p class="card-text"><strong>Recommendation:</strong> {{ form.recommendation ?? "Not available"}}</p>
                <p class="card-text"><strong>Date Done:</strong> {{ formattedDate(form.date_done) ?? "Not available"}}</p>
                <p class="card-text"><strong>Time Done:</strong> {{ form.time_done ?? "Not available"}}</p>
                <p class="card-text"><strong>Remarks:</strong> {{ form.remarks ?? "Not available"}}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
        <div class="modal-footer justify-content-end d-flex gap-2">
          <Button :name="'Confirm Submission'" class="btn btn-primary" @click="submitServiceReport">Confirm Submission</Button>
          <Button :name="'Cancel'" class="btn btn-outline-secondary" @click="closeSubmitService">Cancel</Button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { defineProps, ref,  defineEmits } from 'vue';
import Button from '@/Components/Button.vue';

const props = defineProps({
  form: Object, // Pass the form object to the modal
  ticket: Object,
  id: Object,
});

const form = useForm({
  service_id: props.form.service_id,
  date_started: props.form.date_started,
  time_started: props.form.time_started,
  ticket_number: props.form.ticket_number,
  technician: props.form.technician,
  technician_id: props.id,
  requesting_office: props.form.requesting_office,
  equipment_no: props.form. equipment_no,
  problem: props.form.problem,
  action: props.form.action,
  recommendation: props.form.recommendation,
  date_done: props.form.date_done,
  time_done: props.form.time_done,
  remarks: props.form.remarks,
});

const formattedDate = (dateString) => {
  const date = new Date(dateString);
  const options = { month: 'short', day: '2-digit', year: 'numeric' };
  return date.toLocaleDateString('en-US', options);
};

const emit = defineEmits(['closeSubmitService'])

const closeSubmitService = () => {
  emit('closeSubmitService');
  console.log("Close Button clicked");
};

const submitServiceReport = () => {
  // Implement your logic to submit the service report here
  console.log('Props in ConfirmModal.vue:', props);

  form.post(route('technician.service-report.store'), { preserveScroll: false, preserveState: false });
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

.text {
  text-align: left;
}

.modal-content {
  background-color: #fff;
  padding: 20px; 
  border-radius: 8px;
  text-align: center;
  width: 90%; 
  max-width: 1000px; 
  max-height: 90%;
  overflow: auto;
  font-size: 12px;
}

.modal-title {
  border-radius: 2px;
  text-align: center;
  width: 100%;
  margin-bottom: 10px;
}

.card {
  width: 90%;
  max-width: 100%; 
  margin-bottom: 10px;
}

@media (min-width: 768px) {
  .modal-content {
    padding: 50px; 
  }

  .card {
    width: 100%; 
  }
}
</style>
