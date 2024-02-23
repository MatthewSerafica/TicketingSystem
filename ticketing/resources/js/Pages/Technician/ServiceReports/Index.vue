<template>
  <Header></Header>
  <br />

  <div class="container text-center w-100 h-100 justify-center">
    <h1>View All Service Reports</h1>
    <p>Manage and Track all TMDD Service Reports</p>
    <div class="d-flex gap-2">
      <Link class="text-decoration-none" href="/technician/service-report/create">
        <Button class="rounded btnn secondary large-btn">Create Reports</Button>
      </Link>
    </div>
  </div>

  <div class="search">
    <input
      type="text"
      v-model="searchQuery"
      placeholder="Search Reports..."
      @input="handleSearch"
    />
  </div>

  <div class="table-container">
    <table class="table table-striped">
          <thead class="">
            <tr class="text-center">
              <th>Service No</th>
              <th>Date Started</th>
              <th>Time Started</th>
              <th>Ticket No</th>
              <th>Technician Name</th>
              <th>Requesting Office</th> 
              <th>Equipment No</th>
              <th>Issue</th>
              <th>Action</th>
              <th>Recommendation</th>
              <th>Date Done</th>
              <th>Time Done</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service_report in service_reports" :key="service_report.service_id">
              <td class="text-center py-3">{{ service_report.service_id }}</td>
              <td class="text-center py-3">{{ moment(service_report.date_started).format("YYYY-MM-DD") }}</td>
              <td class="text-center py-3">{{ moment(service_report.time_started, "HH:mm:ss").format("hh:mm A") }}</td>
              <td class="text-center py-3">{{ service_report.ticket_number }}</td>
              <td class="text-center py-3">{{ service_report.technician_name }}</td>
              <td class="text-center py-3">{{ service_report.requesting_office }}</td>
              <td class="text-center py-3">{{ service_report.equipment_no }}</td>
              <td class="text-center py-3">{{ service_report.issue }}</td>
              <td class="text-center py-3">{{ service_report.action }}</td>
              <td class="text-center py-3">{{ service_report.recommendation }}</td>
              <td class="text-center py-3">{{ moment(service_report.date_done).format("YYYY-MM-DD") }}</td>
              <td class="text-center py-3">{{ moment(service_report.time_done, "HH:mm:ss").format("hh:mm A") }}</td>
            </tr>
          </tbody>
        </table>

  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch, onMounted } from "vue";

const props = defineProps({
    service_report: Object,
    technicians: Object,
    filters: Object,

})

const service_reports = ref( props.service_report) ;

</script>

<style scoped>
  .search {
    margin: 10px 0;
    display: flex;
    justify-content: center;
  }

  .search input {
    width: 50%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  .container {
    padding: 20px;
  }

  h1 {
    font-size: 36px;
    margin-bottom: 10px;
  }

  p {
    font-size: 16px;
    margin-bottom: 20px;
  }

  .d-flex {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .btnn{
    width: 150px; 
    height: 50px; 
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .btnn {
    background-color: #aca9b6; 
    color: #fff; 
  }


  .btnn:hover{
    background-color: #898989; 
  }
  .large-btn {
    margin: 10px;
    color: #000000; 
  }

  .table-container {
    margin-top: 20px;
    overflow-x: auto;
  }

  .table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  .table th,
  .table td {
    padding: 12px;
    text-align: center;
  }

  .table th {
    background-color: #ffffff;
    color: #000000;
  }

  .table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  .table tbody tr:hover {
    background-color: #e0e0e0;
  }
  
  
</style>
