<template>
  <div class="content">
    <Header></Header>
    <div class="position-absolute end-0 mt-3 me-3" style="z-index: 100;">
      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown"
        v-if="showSuccessToast" :success="page.props.flash.success" :message="page.props.flash.message"
        @close="handleClose">
      </Toast>

      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showErrorToast"
        :error="page.props.flash.error" :error_message="page.props.flash.error_message" @close="handleClose">
      </Toast>
    </div>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center  main-content">
      <div class="text-center justify-content-center align-items-center d-flex mt-3 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Service Reports</h1>
          <p class="fs-5">Manage and Track all TMDD Service Reports</p>
          <Link :href="route('admin.reports.service-report.create')">
          <Button :name="'Create Service Report'" :color="'primary'"
            class="btn btn-tickets btn-primary py-2 px-5 shadow"></Button>
          </Link>
        </div>
        <div class="input-group mt-3 mb-2">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Report..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div v-if="service_reports.data.length" class="d-flex justify-content-end mb-2 pagination">
        <Pagination :links="service_reports.links" :key="'service_reports'" />
        <br>
      </div>
      <div v-if="service_reports.data.length" class="table-responsive rounded shadow pt-2 px-2">
        <table class="table table-hover custom-rounded-table">
          <thead>
            <tr class="text-start text-muted">
              <th class="text-center text-muted">No.</th>
              <th class="text-muted">Date</th>
              <th class="text-muted">Time Started</th>
              <th class="text-center text-muted">Ticket No</th>
              <th class="text-muted">Technician</th>
              <th class="text-muted">Requesting Office</th>
              <th class="text-center text-muted">Equipment No</th>
              <th class="text-muted">Issue</th>
              <th class="text-muted">Action</th>
              <th class="text-muted">Recommendation</th>
              <th class="text-muted">Date Done</th>
              <th class="text-muted">Time Done</th>
              <th class="text-muted">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service_report in service_reports.data" :key="service_report.service_id" class="align-middle">
              <td class="text-center">{{ service_report.service_id }}</td>
              <td class="text-start" style="width:7rem;">{{ moment(service_report.date_started).format("MMM DD, YYYY") }}</td>
              <td class="text-start" style="width:6rem;">{{ moment(service_report.time_started, "HH:mm:ss").format("hh:mm A") }}</td>
              <td class="text-center">{{ service_report.ticket_number }}</td>
              <td class="text-start" style="width: 12rem;">{{ service_report.technician }}</td>
              <td class="text-start" style="width:8rem;">{{ service_report.requesting_office }}</td>
              <td class="text-center">{{ service_report.equipment_no }}</td>
              <td class="text-start cursor text-truncate" :title="service_report.issue" style="max-width: 8rem;">{{ service_report.issue }}</td>
              <td class="text-start cursor text-truncate" :title="service_report.action" style="max-width: 8rem;">{{ service_report.action }}</td>
              <td class="text-start cursor text-truncate" :title="service_report.recommendation" style="max-width: 10rem;">{{ service_report.recommendation }}
              </td>
              <td class="text-start" style="width: 7rem;">{{ moment(service_report.date_done).format("MMM DD, YYYY") }}</td>
              <td class="text-start" style="width: 6rem;">{{ moment(service_report.time_done, "HH:mm:ss").format("hh:mm A") }}</td>
              <td class="text-start">{{ service_report.remarks }} </td>
            </tr>
          </tbody>
        </table>
      </div>
      <EmptyCard :title="'No service reports yet...'" v-else class="mt-2 w-75" style="height:20rem;">
      </EmptyCard>
    </div>
  </div>

</template>

<script setup>
import Button from '@/Components/Button.vue';
import EmptyCard from '@/Components/EmptyState/Table.vue';
import Pagination from '@/Components/Pagination.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import moment from "moment";
import { ref, watch, watchEffect } from "vue";

Alpine.start()

const page = usePage();

let showSuccessToast = ref(false);
let showErrorToast = ref(false);

watchEffect(() => {
  showSuccessToast.value = !!page.props.flash.success;
  showErrorToast.value = !!page.props.flash.error;
})

const handleClose = () => {
  page.props.flash.success = null;
  page.props.flash.error = null;
  showSuccessToast.value = false;
  showErrorToast.value = false;
}

const props = defineProps({
  service_reports: Object,
  filters: Object,
})

let search = ref(props.filters.search);
let sortColumn = ref("service_id");
let sortDirection = ref("desc");
let timeoutId = null;

const fetchData = () => {
  router.get(
    route('admin.reports.service-reports'),
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
.content {
  width: 100%;
}

.custom-rounded-table {
  border-radius: 10px;
}

.table-responsive {
  width: 90%;
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

.pagination {
  width: 88%;
}


@media (max-width: 1440px) {
  .custom-rounded-table {
    font-size: 12px;
  }

  .content {
    width: 96%;
  }

  .table-responsive {
    max-width: 100%;
    overflow-x: auto;
  }

  .pagination {
    width: 70rem;
  }

  .btn-options {
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media (max-width: 1024px) {

  .table-responsive {
    max-width: 100%;
    overflow-x: auto;
  }

  .custom-rounded-table {
    font-size: 12px;
  }

  .pagination {
    width: 55rem;
  }

  .table-responsive {
    width: 55rem;
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

@media (max-width: 768px) {
  .custom-rounded-table {
    font-size: 12px;
  }

  .table-responsive {
    max-width: 100%;
    overflow-x: auto;
  }
  .pagination {
    width: 40rem;
  }

  .btn-options {
    width: 80px;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media (max-width: 425px) {
  .custom-rounded-table {
    font-size: 10px;
  }

  .content {
    width: 90%;
  }

  .main-content {
    width: 100%;
    margin-left: 6rem;
  }

  .btn-options {
    width: 60px;
  }

  .table-responsive {
    width: 30rem;
    overflow-x: auto;
  }
  .pagination {
    width: 30rem;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}

@media (max-width: 320px) {
  .custom-rounded-table {
    font-size: 10px;
  }

  .content {
    width: 85%;
  }

  .main-content {
    width: 100%;
    margin-left: 6rem;
  }

  .btn-options {
    width: 60px;
  }

  .table-responsive {
    max-width: 100%;
    overflow-x: auto;
  }
  .pagination {
    width: 25rem;
  }

  .custom-rounded-table th,
  .custom-rounded-table td {
    white-space: nowrap;
  }
}
</style>
