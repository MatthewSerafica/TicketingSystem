<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <H1 class="fw-bold">View All Departments and Offices</H1>
          <p class="fs-5"> Manage all Departments</p>
          
          
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <button class="btn btn-secondary px-5 py-2" @click="showDepartments">Departments</button>
            <button class="btn btn-secondary px-5 py-2" @click="showOffices">Offices</button>
          </div>

        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Department or Offices..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>





       <div v-if="showingDepartments" class="w-75">
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
            <tr v-for="department in paginatedItems">
              <td class="text-center py-4">{{ department.id }}</td>
              <td  class="text-center">{{ department.department }}</td>
              <td  class="text-center">{{ formatDate(department.created_at) }}</td>
              <td  class="text-center">{{ formatDate(department.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="departments.data.length" class="flex justify-center w-full mt-6">
            <Pagination :links="departments.links" :key="'departments'" @pageChange="handlePageChange"/>
        </div>
      </div> 



      <div v-else class="w-75">
        <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center"> Office ID</th>
              <th class="text-center">Offices</th>
              <th class="text-center">Date Created</th>
              <th class="text-center">Date Updated</th>
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="office in paginatedItems">
              <td class="text-center py-4">{{ office.id }}</td>
              <td class="text-start py-4">{{ office.office }}</td>
              <td  class="text-center">{{ formatDate(office.created_at) }}</td>
              <td  class="text-center">{{ formatDate(office.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="offices.data.length" class="flex justify-center w-full mt-6">
            <pagination :links="offices.links" :key="'offices'"  @pageChange="handlePageChange" />
        </div>
      </div> 

      



    </div>
  </div>
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import pagination from "@/Components/Pagination.vue";
import Header from "@/Pages/Layouts/AdminHeader.vue";
import moment from "moment";
import { ref, computed } from 'vue'; 

const props = defineProps({
  departments: Object,
  offices: Object,
});

const showingDepartments = ref(true);
const currentPage = ref(1);

const showDepartments = () => {
  showingDepartments.value = true;
};

const showOffices = () => {
  showingDepartments.value = false;
};

const search = ref('');

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};


const handlePageChange = (newPage) => {
  currentPage.value = newPage;
};

const paginatedItems = computed(() => {
  return showingDepartments.value ? props.departments.data : props.offices.data;
});

const paginatedLinks = computed(() => {
  return showingDepartments.value ? props.departments.links : props.offices.links;
});
</script>

<style scoped>

</style>