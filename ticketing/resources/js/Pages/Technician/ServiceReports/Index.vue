<template>
  <div>
  <Header></Header>
  <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
    <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
      <div class="d-flex flex-column justify-content-center align-items-center gap-2">
      <h1 class="fw-bold">View All Service Reports</h1>
      <p class="fs-5">Manage and Track all TMDD Service Reports</p>
      <Link :href="route('technician.service-report.create')">
      <Button :name="'Create Service Report'" :color="'primary'" class="btn btn-tickets btn-primary py-2 px-5"></Button>
      </Link>
    </div>

    <div class="input-group mt-3">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Report..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div v-if="service_reports.data.length" class="d-flex justify-content-end mb-2 pagination">
        <Pagination :links="service_reports.links" :key="'service_reports'" />
        <br>
      </div>
    <div class="table-responsive w-75 rounded shadow">
      <table class="table table-hover custom-rounded-table">
        <thead>
          <tr class="text-center">
            <th class="text-start">Service No</th>
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
        <tbody>
          <tr v-for="service_report in service_reports.data" :key="service_report.service_id" class="align-middle">
              <td class="text-center">{{ service_report.service_id }}</td>
              <td class="text-start">{{ formatDate(service_report.date_started) }}</td>
              <td class="text-start">{{ moment(service_report.time_started, "HH:mm:ss").format("hh:mm A") }}</td>
              <td class="text-center">{{ service_report.ticket_number }}</td>
              <td class="text-start">{{ service_report.technician.user.name }}</td>
              <td class="text-start">{{ service_report.requesting_office }}</td>
              <td class="text-center">{{ service_report.equipment_no }}</td>
              <td class="text-start cursor" :title="service_report.issue">{{ service_report.issue }}</td>
              <td class="text-start cursor" :title="service_report.action">{{ service_report.action }}</td>
              <td class="text-start cursor" :title="service_report.recommendation">{{ service_report.recommendation }}</td>
              <td class="text-start">{{ formatDate(service_report.date_done) }}</td>
              <td class="text-start">{{ moment(service_report.time_done, "HH:mm:ss").format("hh:mm A") }}</td>
              <td class="text-start">{{ service_report.remarks }} </td>
            </tr>
        </tbody>
      </table>
    </div>
  </div>
  </div>
  
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch} from "vue";
import Button from '@/Components/Button.vue'

const props = defineProps({
  service_report: Object,
  filters: Object,

})

const service_reports = ref(props.service_report);

let search = ref(props.filters.search);
let sortColumn = ref("service_id");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = () => {
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

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

</script>

<style scoped>


.table-container {
  margin-top: 20px;
  overflow-x: auto;
}

.btn-tickets {
  transition: all 0.2s;
}

.btn-tickets:hover {
  transform: scale(1.1);
}

.cursor{
  cursor: default;
}

.table-container{
  overflow-x: auto;
}
</style>
