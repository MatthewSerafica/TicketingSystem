<template>
    <div>
      <Header></Header>
      <div class="position-absolute end-0">
        <Toast
          x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
          x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showSuccessToast"
          :success="page.props.flash.success" :message="page.props.flash.message" @close="handleClose">
        </Toast>
  
        <Toast
          x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
          x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showErrorToast"
          :error="page.props.flash.error" :error_message="page.props.flash.error_message" @close="handleClose">
        </Toast>
      </div>
      <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
        <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
          <div class="d-flex flex-column justify-content-center align-items-center gap-2">
            <h1 class="fw-bold">View All Services</h1>
            <p class="fs-5">Manage All Services</p>
            <Link :href="route('admin.services.create')">
              <Button :name="'Add New Service'" :color="'primary'" class="btn btn-tickets btn-primary py-2 px-5"></Button>
            </Link>
          </div>
          <div class="input-group mt-3">
            <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
              placeholder="Search Services..." aria-label="searchIcon" aria-describedby="searchIcon" />
          </div>
        </div>
  
        <div class="w-75">
          <div v-if="services.data.length" class="d-flex justify-content-end mb-2">
            <pagination :links="services.links" :key="'services'" />
            <br>
          </div>
          <table class="table table-hover shadow custom-rounded-table">
            <thead>
              <tr class="text-start">
                <th class="text-center text-muted">Service ID</th>
                <th class="text-muted" style="width: 40%;">Services</th>
                <th class="text-center text-muted">Date Created</th>
                <th class="text-center text-muted">Date Updated</th>
                <th class="text-muted">Delete Option</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="service in services.data" class="align-middle">
                <td class="text-center">{{ service.id }}</td>
                <td style="max-width: 60px;" @dblclick="startEditing(service.id, service.service)">
                  <span v-if="selectedServiceId !== service.id">{{ service.service }}</span>
                  <input v-model="editedService[service.id]" v-if="selectedServiceId === service.id"
                    @keyup.enter="saveService(service.id)" @blur="saveService(service.id)">
                </td>
                <td class="text-center">{{ formatDate(service.created_at) }}</td>
                <td class="text-center">{{ formatDate(service.updated_at) }}</td>
                <td><button type="button" as="button" class="btn btn-danger" @click="showDelete(service)">Delete</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <Delete v-if="isShowDelete" :service="selectedServiceId" @closeDelete="closeDelete"/>
      </div>
    </div>
  </template>
  
  <script setup>
  import Button from '@/Components/Button.vue';
  import Delete from "@/Components/DeleteModal.vue";
  import pagination from "@/Components/Pagination.vue";
  import Toast from '@/Components/Toast.vue';
  import Header from "@/Pages/Layouts/AdminHeader.vue";
  import { Link, router, useForm, usePage } from "@inertiajs/vue3";
  import Alpine from 'alpinejs';
  import moment from "moment";
  import { reactive, ref, watch, watchEffect } from 'vue';
  
  Alpine.start()
  
  const page = usePage();
  
  let showSuccessToast = ref(false);
  let showErrorToast = ref(false);
  
  watchEffect(() => {
    showSuccessToast.value = !!page.props.flash.success;
    showErrorToast.value = !!page.props.flash.error;
  })
  
  const handleClose = () => {
    page.props.flash.success = null;
    page.props.flash.error = null;
    showSuccessToast.value = false;
    showErrorToast.value = false;
  }
  
  const props = defineProps({
    services: Object,
    filters: Object,
  });
  
  const fetchData = () => {
  router.get(
    route('admin.services'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
    },
    {
      preserveState: true,
      replace: true,
    }
  )

}
const resetSorting = () => {
  console.log("Reset Sorting");
  sortColumn.value = "id"
  sortDirection.value = "asc"
}

const debouncedFetchData = () => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
  timeoutId = setTimeout(() => {
    fetchData()
  }, 500)
}

watch(search, () => {
  if (!search.value) {
    resetSorting()
  }
  debouncedFetchData();
})
  
  const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
  };
  
  
  let editedService = reactive({});
  let selectedServiceId = ref(null);
  
  const startEditing = (serviceId, serviceName) => {
    selectedServiceId.value = serviceId;
    if (!editedService[serviceId]) {
      editedService[serviceId] = serviceName;
    }
  };
  
  // Method to save the edited service name
  const saveService = (serviceId) => {
    if (serviceId && editedService[serviceId] !== null) {
      const form = useForm({
        service: editedService[serviceId],
      });
      form.put(route('admin.service.update', { service_id: serviceId }));
      selectedServiceId.value = null;
    }
  };
  
  const isShowDelete = ref(false);
  
  function closeDelete() {
    if(isShowDelete.value) {
      selectedServiceId.value = null
      isShowDelete.value = false;
    }
  }
  
  function showDelete(service) {
    if(!isShowDelete.value) {
      selectedServiceId.value = service;
      isShowDelete.value = true;
    }
  }
  
  
  
  </script>
  
  <style scoped>
  
  .table-responsive {
    overflow-x: auto; 
  }
  
  .btn-tickets {
    transition: all 0.2s;
  }
  
  .btn-tickets:hover {
    transform: scale(1.1);
  }
  
  .btn-options {
    width: 100px;
  }
  
  .custom-rounded-table {
    border-radius: 10px;
  }
  
  
  .ticket-description {
    position: relative;
    cursor: default;
  }
  
  .ticket-description:hover::after {
    content: attr(data-hover-text);
    position: absolute;
    top: 100%;
    left: 0;
    background-color: pink;
    color: red;
    padding: 5px;
    border-radius: 5px;
    z-index: 9999;
  }
  
  @media (max-width: 768px) {
    .custom-rounded-table {
      font-size: 12px;
    }
    .table-responsive {
      overflow-x: auto; 
    }
    
    .btn-options {
      width: 80px; 
    }
  
    .custom-rounded-table th,
    .custom-rounded-table td {
      white-space: nowrap; 
    }
  }
  
  @media (max-width: 576px) {
    
    .custom-rounded-table {
      font-size: 10px; 
    }
    
    .btn-options {
      width: 60px; 
  }
  
  custom-rounded-table th,
    .custom-rounded-table td {
      display: block; 
      width: 100%; 
      text-align: left; 
    }
  }
  </style>