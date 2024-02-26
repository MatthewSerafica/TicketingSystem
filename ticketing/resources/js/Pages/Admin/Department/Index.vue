<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <H1 class="fw-bold">View All Departments</H1>
          <p class="fs-5"> Manage all Departments</p>
          
        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Department..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

       <div class="w-75">
        <div v-if="departments.data.length" class="flex justify-center w-full mt-6">
            <Pagination :links="departments.links" :key="'departments'"/>
            <br>
        </div>
        <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center"> Department ID</th>
              <th class="text-center">Department</th>
              <th class="text-center">Date Created</th>
              <th class="text-center">Date Updated</th>
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="department in departments.data">
              <td class="text-center py-4">{{ department.id }}</td>
              <td  class="text-center">{{ department.department }}</td>
              <td  class="text-center">{{ formatDate(department.created_at) }}</td>
              <td  class="text-center">{{ formatDate(department.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div> 

    </div>
  </div>
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import Header from "@/Pages/Layouts/AdminHeader.vue";
import moment from "moment";
import { ref, computed } from 'vue'; 

const props = defineProps({
  departments: Object,
  offices: Object,
});

const search = ref('');

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

</script>

<style scoped>

</style>