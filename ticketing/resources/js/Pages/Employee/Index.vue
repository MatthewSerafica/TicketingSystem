<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2 ">
          <h1 class="fw-bold">View All Tickets</h1>
          <p class="fs-5"> Manage and Track all TMDD tickets</p>
          <Link :href="route('employee.create')" class="btn btn-tickets btn-primary py-2 px-5">Create New Ticket</Link>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="handleAllButtonClick"></button>
            <Button :name="'Pending'" :color="'secondary'" class="btn-options"
              @click="handlePendingButtonClick"></button>
            <Button :name="'Ongoing'" :color="'secondary'" class="btn-options"
              @click="handleOngoingButtonClick"></button>
            <Button :name="'Resolved'" :color="'secondary'" class="btn-options"
              @click="handleResolvedButtonClick"></button>
          </div>
        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="search"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" v-model="searchQuery" placeholder="Search Tickets..."
            @input="handleSearch" aria-label="search" aria-describedby="search" />
        </div>
      </div>

      <div v-if="tickets.data.length" class="d-flex justify-content-end mb-2 mt-3 pagination">
        <Pagination :links="tickets.links" :key="'tickets'" />
        <br>
      </div>
      <div v-if="tickets.data.length" class="rounded shadow w-75">
        <table class="table table-hover custom-rounded-table">
          <thead>
            <tr class="text-start">
              <th class="text-center text-muted">Ticket No</th>
              <th class="text-center text-muted">Date Created</th>
              <th class="text-center text-muted">Issue</th>
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
              <td class="text-center">
                <div v-for="(assignedTech, index) in ticket.assigned" :key="index">
                  <div v-for="(tech, techIndex) in assignedTech.technician" :key="techIndex">
                    <span>{{ tech.user.name }}</span>
                  </div>
                </div>
              </td>
              <td class="text-center"><span :class="getBadgeColor(ticket.status)" class="p-3">{{ ticket.status
                  }}</span></td>
              <td class="text-center">{{ ticket.sr_no ? ticket.sr_no : 'Unavailable' }}</td>
              <td class="text-center">{{ ticket.resolved_at ? ticket.resolved_at : 'Unresolved' }}</td>
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
import Header from "@/Pages/Layouts/EmployeeHeader.vue";
import { Link, router } from "@inertiajs/vue3";
import moment from "moment";
import { onMounted, ref, watch } from "vue";
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  tickets: Object,
});

const selectedStatus = ref('all');
const filteredTickets = ref(props.tickets);
const tickets = ref(props.tickets);

let search = ref('');
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = () => {
  router.get(
    route('employee.tickets'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      status: selectedStatus.value,
    },
    {
      preserveState: true,
      replace: true,
    }
  )
}

//Button Filtering Actions
const handleAllButtonClick = () => {
  console.log("Handle All Button Click");
  selectedStatus.value = 'all';
  filteredTickets.value = tickets.value;
};

const handlePendingButtonClick = () => {
  console.log("Handle Cancelled Button Click");
  selectedStatus.value = 'pending';
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'Pending');
};

const handleOngoingButtonClick = () => {
  console.log("Handle Cancelled Button Click");
  selectedStatus.value = 'ongoing';
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'Ongoing');
};

const handleResolvedButtonClick = () => {
  console.log("Handle Cancelled Button Click");
  selectedStatus.value = 'resolved';
  filteredTickets.value = tickets.value.filter(ticket => ticket.status === 'Resolved');
};

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

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

onMounted(() => {
  handleAllButtonClick();
});

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
