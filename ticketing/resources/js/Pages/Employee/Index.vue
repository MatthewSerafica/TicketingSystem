<template>
  <div>
    <Header class="sticky-top" style="z-index: 110;"></Header>
    <!--Toast Render-->
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
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2 ">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5"> Manage and Track all TMDD tickets</p>
          <Link :href="route('employee.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket</Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options shadow"
              @click="filterTickets('all')"></Button>
            <Button :name="'New'" :color="'danger'" class="btn-options shadow"
              @click="filterTickets('new')"></Button>
            <Button :name="'Pending'" :color="'warning'" class="btn-options shadow"
              @click="filterTickets('pending')"></Button>
            <Button :name="'Ongoing'" :color="'info'" class="btn-options shadow"
              @click="filterTickets('ongoing')"></Button>
            <Button :name="'Resolved'" :color="'success'" class="btn-options shadow"
              @click="filterTickets('resolved')"></Button>
          </div>
        </div>
        <div class="input-group mt-3 mb-2">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div v-if="tickets.data.length" class="d-flex justify-content-end mb-2 mt-2 pagination">
        <Pagination :links="tickets.links" :key="'tickets'" />
        <br>
      </div>
      <div v-if="tickets.data.length" class="table-responsive rounded shadow pt-2 px-2 mb-3 w-75">
        <table class="table table-hover custom-rounded-table">
          <thead>
            <tr class="text-start">
              <th class="text-center text-muted">No</th>
              <th class="text-center text-muted">Date Created</th>
              <th class="text-center text-muted">Issue</th>
              <th class="text-center text-muted">Service</th>
              <th class="text-center text-muted">Technician</th>
              <th class="text-center text-muted">Status</th>
              <th class="text-center text-muted">SR No.</th>
              <th class="text-center text-muted">Date Resolved</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in tickets.data" :key="ticket.ticket_number" class="align-middle">
              <td class="text-center">{{ ticket.ticket_number }}</td>
              <td class="text-center">{{ formatDate(ticket.created_at) }}</td>
              <td class="text-center cursor" :title="ticket.issue">{{ ticket.issue }}</td>
              <td class="text-center cursor" :title="ticket.issue">{{ ticket.service ? ticket.service : 'N/A' }}</td>
              <td class="text-center">
                <div v-for="(assignedTech, index) in ticket.assigned" :key="index">
                  <div v-for="(tech, techIndex) in assignedTech.technician" :key="techIndex">
                    <span>{{ tech.user.name }}</span>
                  </div>
                </div>
              </td>
              <td class="text-center">
                <span :class="getBadgeColor(ticket.status)" class="btn p-3">
                  {{ ticket.status }}
                </span>
              </td>
              <td class="text-center">{{ ticket.sr_no ? ticket.sr_no : 'N/A' }}</td>
              <td class="text-center">
                {{ isNaN(new Date(formatDate(ticket.resolved_at))) ? 'Not yet resolved' : formatDate(ticket.resolved_at)
                }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <EmptyCard v-else class="mt-2 w-75" style="height:20rem;">
      </EmptyCard>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import EmptyCard from '@/Components/EmptyState/Table.vue';
import Pagination from '@/Components/Pagination.vue';
import Header from "@/Pages/Layouts/EmployeeHeader.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import moment from "moment";
import { nextTick, reactive, ref, watch, watchEffect } from "vue";

// Toast Start
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
// Toast End

const props = defineProps({
  tickets: Object,
  filters: Object,
});

let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = (type, all, ne, pending, ongoing, resolved) => {
  router.get(
    route('employee'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      filterTickets: type,
      all: all,
      new: ne,
      pending: pending,
      ongoing: ongoing,
      resolved: resolved,
    },
    {
      preserveState: true,
      replace: true,
    }
  )

 /*  router.remember({ filter: filter }); */
}

//Sorting
const resetSorting = () => {
  console.log("Reset Sorting");
  sortColumn.value = "ticket_number"
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

const filter = reactive({
  all: true,
  new: false,
  resolved: false,
  pending: false,
  ongoing: false,
})

const filterTickets = async (type) => {
  console.log("Before filter change:", filter);
  if (type === "all") {
    filter.all = true;
    filter.new = false;
    filter.resolved = false;
    filter.pending = false;
    filter.ongoing = false;
  } else if (type === "new") {
    filter.all = false;
    filter.new = true;
    filter.resolved = false;
    filter.pending = false;
    filter.ongoing = false;
  } else if (type === "resolved") {
    filter.all = false;
    filter.new = false;
    filter.resolved = true;
    filter.pending = false;
    filter.ongoing = false;
  } else if (type === "pending") {
    filter.all = false;
    filter.new = false;
    filter.resolved = false;
    filter.pending = true;
    filter.ongoing = false;
  } else if (type === "ongoing") {
    filter.all = false;
    filter.new = false;
    filter.resolved = false;
    filter.pending = false;
    filter.ongoing = true;
  }
  await fetchData(type, filter.all, filter.new, filter.resolved, filter.pending, filter.ongoing);

  await nextTick();
  console.log("After filter change:", filter);
}

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

const getBadgeColor = (status) => {
  switch (status.toLowerCase()) {
    case 'new':
      return 'badge text-bg-danger';
    case 'pending':
      return 'badge text-bg-warning';
    case 'ongoing':
      return 'badge text-bg-primary';
    case 'resolved':
      return 'badge text-bg-success';
    default:
      return 'badge badge-secondary';
  }
};

</script>



<style scoped>
.btn-tickets {
  transition: all 0.2s;
}

.btn-tickets:hover {
  transform: scale(1.1);
}

.btn-options {
  width: 100px;
}

.cursor {
  cursor: default;
}

.pagination {
  width: 90%;
}

.pagination {
  width: 85rem;
}
</style>
