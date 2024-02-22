<template>
  <Header></Header>
  <br>

  <div class="container text-center w-100 h-100 justify-center">
    <h1>View All Users</h1>
    <p>Manage and Track all Users</p>
    <br><br>
    <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
      <button class="btn btn-secondary px-5 py-2" @click="filterUsers('all')">All</button>
      <button class="btn btn-secondary px-4 py-2" @click="filterUsers('employee')">Employees</button>
      <button class="btn btn-secondary px-4 py-2" @click="filterUsers('technician')">Technician</button>
    </div>
    <!-- Add buttons for filtering if needed -->
    <div class="input-group mt-3 mb-4 w-50">
      <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
      <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
        placeholder="Search Users..." aria-label="searchIcon" aria-describedby="searchIcon" />
    </div>
  </div>

  {{ filter }}
  <div>
    <table>
      <thead v-if="!isLoading">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>User Type</th>
          <!-- Add additional headers for extra attributes -->
          <th v-if="filter.employee">Department</th>
          <th v-if="filter.employee">Made Ticket</th>

          <th v-if="filter.technician">Tickets Assigned</th>
          <th v-if="filter.technician">Tickets Resolved</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody v-if="!isLoading">
        <!-- Your table body content remains the same -->
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.user_type }}</td>
          <td v-if="filter.employee">{{ user.employee ? user.employee.department : '' }}</td>
          <td v-if="filter.employee">{{ user.employee ? user.employee.made_ticket : '' }}</td>
          <td v-if="filter.technician">{{ user.technician ? user.technician.tickets_assigned : '' }}</td>
          <td v-if="filter.technician">{{ user.technician ? user.technician.tickets_resolved : '' }}</td>
          <td>{{ formatDate(user.created_at) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { router } from '@inertiajs/vue3';
import moment from "moment";
import { defineProps, nextTick, reactive, ref, watch } from 'vue';

const props = defineProps({
  users: Object,
  filter: Object,
});

let search = ref(props.filter.search);
let sortColumn = ref("id");
let sortDirection = ref("asc");
let timeoutId = null;
let isLoading = ref(false);

const fetchData = async (type) => {
  isLoading = true;
  await router.get(
    route('admin.users'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      filterUsers: type,
    },
    {
      preserveScroll: true,
      replace: true,
    }
  );
  // Update the filter state after fetching data
  filter.all = type === "all";
  filter.employee = type === "employee";
  filter.technician = type === "technician";
  router.remember({ filter: filter });
  await nextTick();
  console.log("After data fetch and filter update:", filter);
  isLoading = false;
}


const resetSorting = () => {
  sortColumn.value = "id";
  sortDirection.value = "asc";
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
  employee: false,
  technician: false,
})

const filterUsers = async (type) => {
  console.log("Before filter change:", filter);
  if (type === "all") {
    filter.all = true;
    filter.employee = false;
    filter.technician = false;
  } else if (type === "employee") {
    filter.all = false;
    filter.employee = true;
    filter.technician = false;
  } else if (type === "technician") {
    filter.all = false;
    filter.employee = false;
    filter.technician = true;
  }
  await fetchData(type); // Pass the type to fetchData and wait for it to complete
  // Use nextTick to log the updated state after the next DOM update
  await nextTick();
  console.log("After filter change:", filter);
}

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
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

/* Add your custom styles */
</style>
