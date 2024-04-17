<template>
    <div class="custom-modal" @close="Print">
      <div class="modal-content d-flex flex-column">
        <div class="d-flex flex-row justify-content-end">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closePrint"></button>
        </div>

        <div class="d-flex flex-column justify-content-center align-items-center">
          <div class="parent-content mt-2 d-flex justify-content-between w-75 align-items-center">
          <div>
            <SchoolLogo></SchoolLogo>
          </div>

            <div class="mt-2 d-flex flex-column">
              <h2 >Saint Louis University</h2>
              <h2 >Technology Management and</h2>
              <h2 >Development Department</h2>
              <div class="d-flex justify-content-between" v-if="service_report">
                <p>SERVICE REPORT FORM</p>
              <strong>{{ service_report.service_report }}</strong>
              </div>
            </div>

            <div>
            <table class="bordered-table">
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
                <p v-if="service_report" class="d-flex justify-content-end">
                    <!-- <span>SERVICE REPORT FORM</span>  -->
                    <span>SR-A-{{ props.service_report.service_id }}</span>
                </p>

              <div class="bordered-section">
                <div class="content-group">
                  <span class="content-group-text bordered-column"><Strong>Date Started:</Strong>{{ moment(props.service_report.date_started).format("MMM DD, YYYY") }}</span>
                  <span class="content-group-text bordered-column"><Strong>Time Started:</Strong>{{ moment(props.service_report.time_started, "HH:mm:ss").format("hh:mm A") }}</span>
                  <span class="content-group-text bordered-column"><Strong>Ticket Number:</Strong> {{ props.service_report.ticket_number }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" colspan="2"><Strong>Name of Technician:</Strong> {{ props.service_report.technician }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" colspan="2"><Strong>Requesting Office:</Strong> {{ props.service_report.requesting_office }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" colspan="2"><Strong>Equipment, Property Tag/Serial No.:</Strong> {{ props.service_report.equiptment_no }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" colspan="2"><Strong>Problem Encountered:</Strong> {{ props.service_report.issue }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" colspan="2"><Strong>Action Taken:</Strong> {{ props.service_report.service }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text bordered-column" colspan="2"><Strong>Recommendation:</Strong> {{ props.service_report.recommendation }}</span>
                </div>
              </div>
  
              <h3 class="section-title">Client Verification of Service Rendered</h3>
              <div class="bordered-section">
                
                <div class="content-group">
                  <span class="content-group-text"><Strong>Status of equipment after service:</Strong> </span>
                </div>
                <div class="content-group">
                  <span class="content-group-text"><Strong>Name and Signature of Client:</Strong> </span>
                </div>
              </div>
              
              <h3 class="section-title">Technician Activity Report</h3>
              <div class="bordered-section">
                
                <div class="content-group">
                  <span class="content-group-text"><Strong>Date Done:</Strong> {{ moment(props.service_report.date_done).format("MMM DD, YYYY") }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text"><Strong>Time Done:</Strong> {{ moment(props.service_report.time_done, "HH:mm:ss").format("hh:mm A") }}</span>
                </div>
                <div class="content-group">
                  <span class="content-group-text"><Strong>Technician's Signature:</Strong> </span>
                </div>
              </div>
            </div>
          </div>
        </div></div>
        
        <div class="d-flex flex-row gap-2 justify-content-end mt-2">
          <button @click="printDocument" type="button" class="btn btn-primary btn-print">Print</button>
          <button @click="closePrint" type="button" as="button" class="btn btn-outline-secondary btn-print ">Cancel</button>
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
  
    .modal-content {
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      text-align: center;
      max-width: 80%;
      max-height: 100%;
      overflow: auto;
    }

    h2, h3 {
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
  
    @media print {
    .btn-print {
      display: none;
    }
  }

  .bordered-table {
  border-collapse: collapse;
}

.bordered-table td {
  border: 1px solid #ccc;
  padding: 8px;
}


  </style>
  