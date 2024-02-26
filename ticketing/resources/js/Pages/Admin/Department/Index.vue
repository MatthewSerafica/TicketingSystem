<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <H1 class="fw-bold">View All Departments and Offices</H1>
          <p class="fs-5"> Manage all Departments</p>
          
          
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <button class="btn btn-secondary px-5 py-2">Departments</button>
            <button class="btn btn-secondary px-5 py-2">Offices</button>
          </div>

        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Tickets..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>
       <div class="w-75">
        <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center"> Department ID</th>
              <th>Department</th>
              <th>Date Created</th>
              <th>Date Updated</th>
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="department in departments">
              <td class="text-center py-4">{{ department.id }}</td>
              <td class="text-start py-4">{{ department.department }}</td>
              <td class="text-start py-4">{{ formatDate(department.created_at) }}</td>
              <td class="text-start py-4">{{ formatDate(department.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div> 
      <div class="w-75">
        <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center"> Office ID</th>
              <th>Offices</th>
              <th>Date Created</th>
              <th>Date Updated</th>
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="office in offices">
              <td class="text-center py-4">{{ office.id }}</td>
              <td class="text-start py-4">{{ office.office }}</td>
              <td class="text-start py-4">{{ formatDate(office.created_at) }}</td>
              <td class="text-start py-4">{{ formatDate(office.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div> 

    </div>
  </div>
</template>
<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import moment from "moment";
import { nextTick, reactive, ref, watch } from "vue";

const props = defineProps({
  departments: Object,
  offices: Object,
});

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

</script>

<style scoped>

</style>