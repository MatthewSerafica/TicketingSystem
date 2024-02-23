<template>
  <Header></Header>
  <br />

  <div class="container text-center w-100 h-100 justify-center">
    <h1>View All Service Reports</h1>
    <p>Manage and Track all TMDD Service Reports</p>
    <div class="d-flex gap-2">
      <Link class="text-decoration-none" href="/technician/service-report/create">
        <Button class="rounded btnn secondary large-btn">Create Reports</Button>
      </Link>
    </div>
    <button class="ticket-button">Open</button>
    <button class="ticket-button">In Progress</button>
    <button class="ticket-button">Resolved</button>
  </div>

  <div class="search">
    <input
      type="text"
      v-model="searchQuery"
      placeholder="Search Reports..."
      @input="handleSearch"
    />
  </div>

  <div class="table-container"></div>
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch, onMounted } from "vue";

const props = defineProps({
    service_report: Object,
    technicians: Object,
    filters: Object,

})

const reports = ref( props.serviceReport) ;

</script>

<style>
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

  .d-flex {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .btnn{
    width: 150px; 
    height: 50px; 
    font-size: 16px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .btnn {
    background-color: #2f1596; 
    color: #fff; 
  }


  .btnn:hover{
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
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .ticket-button:hover {
    background-color: #6e5300;
    color: #e7e7e7;
  }

  .large-btn {
    margin: 10px;
  }
</style>
