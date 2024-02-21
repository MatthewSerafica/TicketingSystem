<template>
  <Header></Header>
  <br>
  {{ users }}
  {{ employees }}

  <div class="container text-center w-100 h-100 justify-center">
    <h1>View All Users</h1>
    <p>Manage and Track all Users</p>
    <br><br>
    <!-- Add buttons for filtering if needed -->
    <div class="search">
      <input type="text" v-model="searchQuery" placeholder="Search Employees..." @input="handleSearch" />
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
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.user_type }}</td>
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
  employees: Object,
});

console.log(props.users)

const searchQuery = ref(""); 
let users = ref([]); 

const filteredUsers = ref([]);


// Fetch users who are either employees or technicians
onMounted(async () => {
  try {
    const response = await User.getAllUsers(); 
    users.value = response.data; 
  } catch (error) {
    console.error("Error fetching users:", error);
  }
});

const handleSearch = () => {
  // Implement your search logic here
  // For simplicity, you can filter users based on name or email
  filteredUsers.value = users.value.filter(user =>
    (user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    user.email.toLowerCase().includes(searchQuery.value.toLowerCase())) &&
    user.user_type !== 'admin' // Filter out admin users
  );
};


// Format date function
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
