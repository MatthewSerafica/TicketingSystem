<template>
  <div>
    <Header></Header>
    <br />

    <div class="title-container">
      <h1>Create Tickets</h1>
    </div>

    <div class="create-ticket">
      <div>
        <p>Title</p>
        <input type="text" placeholder="Enter Ticket Title..." />
      </div>
      <div>
        <p>Description</p>
        <input type="text" placeholder="Enter Ticket Description..." />
      </div>
    </div>
    
    <div class="button-container">
      <button type="button" @click="submitForm" class="submit-button">Submit</button>
      <Link href="/technician/tickets">
        <button type="button" class="cancel-button">Cancel</button>
      </Link>
    </div>

    <!-- Add class to the container element if needed -->
    <div class="table-container"></div>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router } from "@inertiajs/vue3";
</script>

<style>
* {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.title-container {
  text-align: center;
}

.create-ticket {
  margin: 10px 0;
  display: flex;
  align-items: center;
  flex-direction: column;
}

.create-ticket div {
  margin-bottom: 20px;
  width: 80%;
}

.create-ticket input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

/* Add styling for create-ticket-link */
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

.cancel-button {
  background-color: #000066;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button {
  width: 10%;
  background-color: #000066;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  margin-right: 10px; /* Add margin to create space */
  transition: background-color 0.3s;
}

.cancel-button:hover,
.submit-button:hover {
  background-color: #0c0c36; 
}
</style>
