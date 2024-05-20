<template>
  <div class="custom-modal" @close="Print">
    <div class="modal-content d-flex flex-column">
      <div class="d-flex flex-row justify-content-end print-hidden">
        <button type="button" class="btn-close print-hidden" data-bs-dismiss="modal" aria-label="Close"
          @click="closePrint"></button>
      </div>

      <div class="d-flex flex-column justify-content-center">

        <div class="parent-content d-flex justify-content-between align-items-center">
          <div>
            <SchoolLogo class="school-logo"></SchoolLogo>
          </div>

          <div class="d-flex flex-column align-items-center">
            <div class="text-center">
              <h6 class="school-name mb-1 fw-semibold">Saint Louis University</h6>
              <h6 class="school-name mb-1 fw-semibold">Technology Management and</h6>
              <h6 class="school-name mb-1 fw-semibold">Development Department</h6>
            </div>
            <div class="d-flex justify-content-between form-name fw-bold" v-if="service_report">
              <h6 class="form-name fw-bold">SERVICE REPORT FORM</h6>
              <strong>{{ service_report.service_report }}</strong>
            </div>
          </div>

          <div class="tab">
            <table class="bordered-table text-start">
              <tr>
                <td>Document code</td>
                <td>FM-TMD-001</td>
              </tr>
              <tr>
                <td>Revision No.</td>
                <td>00</td>
              </tr>
              <tr>
                <td>Effectivity</td>
                <td>Feb 01, 2021</td>
              </tr>
              <tr>
                <td>Page</td>
                <td>1 of 1</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="report-content w-100">
          <div class="row justify-content-center mb-4">
            <div class="col-md-6">
              <div class="d-flex justify-content-end align-items-center gap-3 me-2" v-if="service_report">
                <h6 class="fw-bold fs-6 ">
                  SR-A-
                </h6>
                <h6 class="fw-normal fs-5">{{ props.service_report.service_id }}</h6>
              </div>

              <div class="bordered-section">
                <div class="content-group">
                  <span class="content-group-text bordered-column">
                    <strong>Date Started:</strong>
                    <br>
                    {{ moment(props.service_report.date_started).format("MMM DD, YYYY") }}
                  </span>
                  <span class="content-group-text bordered-column">
                    <strong>Time Started:</strong>
                    <br>
                    {{ moment(props.service_report.time_started, "HH:mm:ss").format("hh:mm A") }}
                  </span>
                  <span class="content-group-text bordered-column">
                    <strong>Ticket Number:</strong>
                    <br>
                    {{ props.service_report.ticket_number }}
                  </span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" style="width: 20rem;">
                    <strong>Name of Technician:</strong>
                    <br>
                    {{ props.service_report.technician }}
                  </span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column">
                    <strong>Requesting Office:</strong>
                    <br>
                    {{ props.service_report.requesting_office }}
                  </span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" style="width: 20rem;">
                    <strong>Equipment, Property Tag/Serial No.:</strong>
                    <br>
                    {{ props.service_report.equipment_no }}
                  </span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column">
                    <strong>Problem Encountered:</strong>
                    <br>
                    {{ props.service_report.issue }}
                  </span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column">
                    <strong>Action Taken:</strong>
                    <br>
                    {{ props.service_report.action }}
                  </span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" style="width: 20rem;">
                    <strong>Recommendation:</strong>
                    <br>
                    {{ props.service_report.recommendation }}
                  </span>
                </div>
              </div>


              <h3 class="section-title">Client Verification of Service Rendered</h3>
              <div class="bordered-section">
                <div class="content-group">
                  <span class="text-start fw-bold" style="width: 10rem;">
                    Status of equipment after service:
                    <br>
                    <br>
                    {{ props.service_report.status_after_service }}
                  </span>
                </div>
                <div class="content-group mb-4">
                  <span class="content-group-text">
                    <strong>Name and Signature of Client:</strong>
                    {{ props.service_report.client_name_signature }}
                  </span>
                </div>
              </div>


              <h3 class="section-title">Technician Activity Report</h3>
              <div class="d-flex">
                <div class="bordered-section" style="width: 8rem;">
                  <h6 style="font-size: 10px;">
                    I hereby certify that the above statements are true
                  </h6>
                </div>
                <div class="bordered-section">
                  <div class="content-group">
                    <span class="content-group-text">
                      <strong>Date Done:</strong>
                      <br>
                      {{ moment(props.service_report.date_done).format("MMM DD, YYYY") }}
                    </span>
                    <span class="content-group-text">
                      <strong>Time Done:</strong>
                      <br>
                      {{ moment(props.service_report.time_done, "HH:mm:ss").format("hh:mm A") }}
                    </span>
                  </div>
                  <div class="content-group">
                    <span class="content-group-text" style="width: 10rem;">
                      <strong>Technician's Signature:</strong>
                      <br>
                      {{ props.service_report.technician_signature }}
                    </span>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex flex-row gap-2 justify-content-end mt-2">
        <button @click="printDocument" type="button" class="btn btn-primary btn-print">Print</button>
        <button @click="closePrint" type="button" as="button"
          class="btn btn-outline-secondary btn-print ">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { defineEmits } from 'vue';
import { defineProps } from 'vue';
import moment from "moment";
import SchoolLogo from '@/Components/SchoolLogo.vue'

const emit = defineEmits(['closePrint']);

const printDocument = () => {
  window.print(); // Initiate the printing process
};

const closePrint = () => {
  emit('closePrint');
};

const props = defineProps({
  service_report: Object,
});

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

@media print {
  .custom-modal {
    overflow: hidden;
  }

  .print-hidden {
    display: none;
  }

  .tab {
    margin-top: -1rem;
  }

  .school-logo {
    width: 5rem;
    height: 5rem;
  }

  .school-name {
    font-size: 11px;
  }

  .form-name {
    font-size: 11px;
  }

  .modal-content {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    font-size: 50%;
    overflow-y: hidden;
  }

  .btn-print {
    display: none;
  }

  .parent-content {
    gap: 1rem;
  }

}

.modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  text-align: center;
  max-width: 80%;
  max-height: 80%;
  overflow: auto;
}

h2,
h3 {
  font-size: 15px;
}

.btn-close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
}

.bordered-section {
  border: 1px solid black;
  padding: 5px;
  margin-bottom: 10px;
}

.section-title {
  margin-top: 0;
  margin-bottom: 10px;
  font-size: 11px;
}

.content-group {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
}

.content-group:not(:last-child) {
  border-bottom: 1px solid black;
  padding-bottom: 0px;
  margin-bottom: 10px;
}


.content-group-text {
  flex: 2;
  text-align: left;
  display: inline;
}

.bordered-column {
  border-right: 1px solid black;
  padding-right: 5px;
  margin-right: 5px;
}

.bordered-column:last-child {
  border-right: none;
  padding-right: 0;
  margin-right: 0;
}

.bordered-table {
  border-collapse: collapse;
}

.bordered-table td {
  border: 1px solid #5c5c5c;
}
</style>