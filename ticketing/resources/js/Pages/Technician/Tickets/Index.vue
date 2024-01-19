
<template>
  <div>
    <Header></Header>
    <br>

    <div class="search">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search Tickets..."
        @input="handleSearch"
      />
    </div>

    <div class="container text-center w-100 h-100 justify-center">
      <h1>View All Tickets</h1>
      <p>Manage and Track all TMDD tickets</p>
      <Link :href="`/technician/tickets/create`" class="create-ticket-link">Create New Ticket</Link>
      <br><br>
      <button class="ticket-button" @click="filterTickets('All')">All</button>
      <button class="ticket-button" @click="filterTickets('New')">New</button>
      <button class="ticket-button" @click="filterTickets('Pending')">Pending</button>
      <button class="ticket-button" @click="filterTickets('Resolved')">Resolved</button>
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
          <tr v-for="ticket in filteredTickets" :key="ticket.id">
            <td>{{ ticket.ticketNo }}</td>
            <td>{{ ticket.employee }}</td>
            <td>{{ ticket.department }}</td>
            <td>{{ ticket.issue }}</td>
            <td>{{ ticket.technician }}</td>
            <td>{{ ticket.status }}</td>
            <td>{{ ticket.dateIssued }}</td>
            <td>{{ ticket.dateResolved }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue"
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

Link.create-ticket-link {
  display: inline-block;
  padding: 10px 20px;
  background-color: #000000; /* Green */
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  transition: background-color 0.3s;
}

Link.create-ticket-link:hover {
  background-color: #898989;
}

.ticket-button {
  width: 10%;
  margin-right: 10px;
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  background-color: #cecece;
  color: #1e1e1e;
  border-radius: 8px; /* Adjust border-radius for rounded edges */
  cursor: pointer;
  transition: background-color 0.3s;
}

.ticket-button:hover {
  background-color: #898989;
  color: #e7e7e7;
}
</style>
