<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <H1 class="fw-bold">View All Offices</H1>
          <p class="fs-5"> Manage all Offices</p>
          <Link :href="route('admin.office.create')" class="btn btn-tickets btn-primary py-2 px-5">Add Office
          </Link>
        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Offices..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>



      <div class="w-75">
        <div v-if="offices.data.length" class="flex justify-center w-full mt-6">
            <pagination :links="offices.links" :key="'offices'"/>
            <br>
        </div>
        <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center"> Office ID</th>
              <th>Offices</th>
              <th class="text-center">Date Created</th>
              <th class="text-center">Date Updated</th>
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="office in offices.data">
              <td class="text-center py-4">{{ office.id }}</td>
              <td class="text-start py-4">{{ office.office }}</td>
              <td  class="text-center">{{ formatDate(office.created_at) }}</td>
              <td  class="text-center">{{ formatDate(office.updated_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div> 

    </div>
  </div>
</template>
<script setup>
import pagination from "@/Components/Pagination.vue";
import Header from "@/Pages/Layouts/AdminHeader.vue";
import moment from "moment";
import { Link, router, useForm } from "@inertiajs/vue3";
import { ref, computed } from 'vue'; 

const props = defineProps({
  offices: Object,
});

const search = ref('');

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

</script>

<style scoped>

</style>