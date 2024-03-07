<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Service Reports</h1>
          <p class="fs-5">Manage and Track all TMDD Service Reports</p>
          <Link :href="route('admin.reports.service-report.create')">
          <Button :name="'Create Service Report'" :color="'primary'"
            class="btn btn-tickets btn-primary py-2 px-5"></Button>
          </Link>
        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Report..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div class="table-responsive" style="padding: 0 30px;">
        <table class="table table-hover shadow custom-rounded-table">
          <thead>
            <tr class="text-start">
              <th class="text-center">No.</th>
              <th>Date</th>
              <th>Time Started</th>
              <th class="text-center">Ticket No</th>
              <th>Technician</th>
              <th>Office</th>
              <th class="text-center">Equipment No</th>
              <th>Issue</th>
              <th>Action</th>
              <th>Recommendation</th>
              <th>Date Done</th>
              <th>Time Done</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service_report in service_reports" :key="service_report.service_id" class="align-middle">
              <td class="text-center">{{ service_report.service_id }}</td>
              <td class="text-start">{{ moment(service_report.date_started).format("MMM DD, YYYY") }}</td>
              <td class="text-start">{{ moment(service_report.time_started, "HH:mm:ss").format("hh:mm A") }}</td>
              <td class="text-center">{{ service_report.ticket_number }}</td>
              <td class="text-start">{{ service_report.technician.user.name }}</td>
              <td class="text-start">{{ service_report.requesting_office }}</td>
              <td class="text-center">{{ service_report.equipment_no }}</td>
              <td class="text-start cursor text-truncate" :title="service_report.issue" style="max-width: 10rem;">{{ service_report.issue }}</td>
              <td class="text-start cursor text-truncate" :title="service_report.action" style="max-width: 10rem;">{{ service_report.action }}</td>
              <td class="text-start cursor text-truncate" :title="service_report.recommendation" style="max-width: 10rem;">{{ service_report.recommendation }}
              </td>
              <td class="text-start">{{ moment(service_report.date_done).format("MMM DD, YYYY") }}</td>
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
import Button from '@/Components/Button.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch } from "vue";

const props = defineProps({
  service_reports: Object,
  //filters: Object,

})

let search = ref(props.search);
let sortColumn = ref("service_id");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = () => {
  router.get(
    route('admin.service-reports'),
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
.custom-rounded-table {
  border-radius: 10px;
}

.table-responsive {
  width: 110rem;
  overflow-x: auto;
}

.btn-tickets {
  transition: all 0.2s;
}

.btn-tickets:hover {
  transform: scale(1.1);
}

.cursor {
  cursor: default;
}

@media only screen and (max-width: 768px) {
  .custom-rounded-table {
    font-size: 12px;
  }

  .table-responsive {
    width: 50rem;
    overflow-x: auto;
  }

  .btn-options {
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media only screen and (max-width: 1024px) {
  .custom-rounded-table {
    font-size: 12px;
  }

  .table-responsive {
    width: 60rem;
    overflow-x: auto;
  }

  .btn-options {
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media only screen and (max-width: 1440px) {
  .custom-rounded-table {
    font-size: 12px;
  }

  .table-responsive {
    width: 85rem;
    overflow-x: auto;
  }

  .btn-options {
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media only screen and (max-width: 576px) {
  .custom-rounded-table {
    font-size: 10px;
  }

  .btn-options {
    width: 60px;
  }

  .table-responsive {
    width: 50rem;
    overflow-x: auto;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    display: block;
    width: 100%;
    text-align: left;
  }
}
</style>
