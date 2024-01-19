<template>
    <div>
      <Header></Header>
      <br />
  
      <div class="title-container">
        <h1>View All Tickets</h1>
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
        <button class="submit-ticket-button">Submit</button>
        <Link :href="`/admin/tickets`" class="create-ticket-link">Cancel</Link>
      </div>
  
      <div class="table-container"></div>
    </div>
  </template>
  
  <script setup>
  import Header from "@/Pages/Layouts/AdminHeader.vue";
  import {Link, router} from "@inertiajs/vue3"
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
  
  .container {
    padding: 20px;
  }
  
  h1 {
    font-size: 36px;
    margin-bottom: 10px;
  }
  
  p {
    justify-content: start;
    font-size: 16px;
    margin-bottom: 10px;
  }
  
  .button-container {
    display: flex;
    justify-content: center;
  }

  .submit-ticket-button {
  width: 10%;
  margin-right: 10px;
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  background-color: #000066;
  color: #fff;
  border-radius: 8px; /* Adjust border-radius for rounded edges */
  cursor: pointer;
  transition: background-color 0.3s;
}
  
  </style>
  