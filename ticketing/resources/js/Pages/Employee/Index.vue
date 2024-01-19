<template>
    <Header></Header>
    <br>

    <div class="search">
        <input type="text" v-model="searchQuery" placeholder="Search Tickets..." @input="handleSearch"/>
    </div>

    <div class="container text-center w-100 h-100 justify-center">
        <H1>View All Tickets</H1>
        <p> Manage and Track all TMDD tickets</p>
        <Link :href="`/employee/create`" class="create-ticket-link">Create New Ticket</Link>
        <br><br>
        <button class="ticket-button">All</button>
        <button class="ticket-button">New</button>
        <button class="ticket-button">Pending</button>
        <button class="ticket-button">Resolved</button>
    </div>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Ticket No</th>
            <th>Employee</th>
            <th>Department</th>
            <th>Issue</th>
            <th>Technician</th>
            <th>Status</th>
            <th>Date Issued</th>
            <th>Date Resolved</th>
          </tr>
        </thead>
        <tbody>
          <!-- Table rows with data will go here -->
        </tbody>
      </table>
    </div>
</template>
<script setup>
import Header from "@/Pages/Layouts/EmployeeHeader.vue"
import {Link, router} from "@inertiajs/vue3"


</script>


<style>
* {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}


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

.create-ticket-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #000066; 
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.create-ticket-link:hover {
  background-color: #898989;
}

.ticket-button {
  width: 10%;
  margin-right: 10px;
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  background-color: #CC9900;
  color: #1e1e1e;
  border-radius: 8px; /* Adjust border-radius for rounded edges */
  cursor: pointer;
  transition: background-color 0.3s;
}

.ticket-button:hover {
  background-color: #6e5300;
  color: #e7e7e7;
}
.table-container{
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
}

table {   
      width: 80%;
      border-collapse: collapse;
      margin-top: 20px;
      margin: auto;
    }

    th, td {
  border: 1px solid #dddddd;
  text-align: center;
  padding: 8px;
}

th {
  background-color: #f2f2f2;
}

</style>
