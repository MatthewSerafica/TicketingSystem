<template>
  <Header></Header>
  <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Service Reports</h1>
          <p class="fs-5">Manage and Track all TMDD Service Reports</p>
          <Link class="btn btn-tickets btn-primary py-2 px-5" :href="route('technician.service-report.create')">Create Report</Link>
        </div>

  <div class="container text-center w-100 h-100 justify-center">
    <h1>View All Service Reports</h1>
    <p>Manage and Track all TMDD Service Reports</p>
    <div class="d-flex gap-2">
      <Link :href="route('technician.service-report.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Reports </Link>

    </div>
    <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

  <div class="w-75">
    <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center">Service No</th>
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
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody class="">
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
              <td class="text-center py-3">{{ service_report.remarks }} </td>
            </tr>
          </tbody>
        </table>

  </div>
  </div>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router, useForm  } from "@inertiajs/vue3";
import moment from "moment";
import { nextTick, reactive, ref, watch, onMounted } from "vue";
import Button from '@/Components/Button.vue'

const props = defineProps({
    service_report: Object,
    technicians: Object,
    //filters: Object,

})

const service_reports = ref( props.service_report) ;

let search = ref(props.search);
let sortColumn = ref("service_id");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = (type) => {
  router.get(
    route('technician.service-reports'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
    },
    {
      preserveState: true,
      replace: true,
    }
  )

}


const resetSorting = () => {
  console.log("Reset Sorting");
  sortColumn.value = "service_id"
  sortDirection.value = "asc"
}

const debouncedFetchData = () => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
  timeoutId = setTimeout(() => {
    fetchData()
  }, 500)
}

watch(search, () => {
  if (!search.value) {
    resetSorting()
  }
  debouncedFetchData();
})

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
  
  .btn-tickets {
  transition: all 0.2s;
}

.btn-tickets:hover {
  transform: scale(1.1);
}
.btn-options {
  width: 100px;
}
</style>
