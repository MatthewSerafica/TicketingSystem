<template>
  <Header></Header>
  {{ users }}
{{ users[1].employee.department }}
  <br>

  <div class="container text-center w-100 h-100 justify-center">
    <h1>View All Users</h1>
    <p>Manage and Track all Users</p>
    <br><br>
    <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
      <button class="btn btn-secondary px-5 py-2" @click="handleAllButtonClick">All</button>
      <button class="btn btn-secondary px-4 py-2" @click="handleEmployeesButtonClick">Employees</button>
      <button class="btn btn-secondary px-4 py-2" @click="handleTechnicianButtonClick">Technician</button>
      </div>
    <!-- Add buttons for filtering if needed -->
    <div class="input-group mt-3 mb-4 w-50">
      <span class="input-group-text" id="search"><i class="bi bi-search"></i></span>
      <input type="text" class="form-control py-2" v-model="searchQuery" placeholder="Search Employees..."
        @input="handleSearch" aria-label="search" aria-describedby="search" />
    </div>
  </div>

  <div>
    <table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>User Type</th>
      <!-- Add additional headers for extra attributes -->
      <th v-if="hasEmployeeType">Department</th>
      <th v-if="hasEmployeeType">Made Ticket</th>
      <th v-if="hasTechnicianType">Tickets Assigned</th>
      <th v-if="hasTechnicianType">Tickets Resolved</th>
      <th>Created At</th>
    </tr>
  </thead>
  <tbody>
    <!-- Your table body content remains the same -->
    <tr v-for="user in filteredUsers" :key="user.id">
      <td>{{ user.id }}</td>
      <td>{{ user.name }}</td>
      <td>{{ user.email }}</td>
      <td>{{ user.user_type }}</td>
      <!-- Render additional attributes based on user type -->
      <td v-if="hasEmployeeType">{{ user.employee ? user.employee.department : '' }}</td>
      <td v-if="hasEmployeeType">{{ user.employee ? user.employee.made_ticket : '' }}</td>
      <td v-if="hasTechnicianType">{{ user.technician ? user.technician.tickets_assigned : '' }}</td>
      <td v-if="hasTechnicianType">{{ user.technician ? user.technician.tickets_resolved : '' }}</td>
      <td>{{ formatDate(user.created_at) }}</td>
    </tr>
  </tbody>
</table>

  </div>
</template>

<script setup>
import { defineProps, ref, onMounted } from 'vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import moment from "moment";

  const props = defineProps({
    users: Object,
  });

  const users = ref(props.users);
  const filteredUsers = ref([]);

  const hasTechnicianType = computed(() => {
    // Check if any user in the list has a technician type
    return props.users.some(user => user.user_type === 'technician');
  });

  const hasEmployeeType = computed(() => {
    // Check if any user in the list has an employee type
    return props.users.some(user => user.user_type === 'employee');
  });

const handleSearch = () => {
  console.log("Search clicked");
  // Filter users based on search query
  filteredUsers.value = users.value.filter(user =>
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
};

// Method to filter users based on user type
const filterUsersByType = (type) => {
  console.log("filterUsersByType clicked");
  filteredUsers.value = users.value.filter(user => user.user_type.toLowerCase() === type.toLowerCase());
};

// Method to show all users
const showAllUsers = () => {
  console.log("allusers clicked");
  filteredUsers.value = users.value;
};

// Method to handle click on "All" button
const handleAllButtonClick = () => {
  console.log("all button clicked");
  showAllUsers();
};

// Method to handle click on "Employees" button
const handleEmployeesButtonClick = () => {
  console.log("employee clicked");
  filteredUsers.value = users.value.filter(user => user.user_type.toLowerCase() === 'employee');
};


// Method to handle click on "Technician" button
const handleTechnicianButtonClick = () => {
  console.log("technician clicked");
  filteredUsers.value = users.value.filter(user => user.user_type.toLowerCase() === 'technician');
};

onMounted(() => {
  console.log("onmounted function");
  // Show all users by default when the component is mounted
  showAllUsers();
});

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
