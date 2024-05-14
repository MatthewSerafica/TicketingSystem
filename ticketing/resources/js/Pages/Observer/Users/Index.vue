<template>
  <div>
    <Header class="sticky-top" style="z-index: 110;"></Header>
    <div class="position-absolute end-0">
      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown"
        v-if="showSuccessToast" :success="page.props.flash.success" :message="page.props.flash.message"
        @close="handleClose">
      </Toast>

      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showErrorToast"
        :error="page.props.flash.error" :error_message="page.props.flash.error_message" @close="handleClose">
      </Toast>
    </div>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-3 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-1">
          <h1 class="fw-bold">View All Users</h1>
          <p class="fs-5">Manage All Users</p>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options shadow" @click="filterUsers('all')"></Button>
            <Button :name="'Employees'" :color="'secondary'" class="btn-options shadow"
              @click="filterUsers('employee')"></Button>
            <Button :name="'Technicians'" :color="'secondary'" class="btn-options shadow"
              @click="filterUsers('technician')"></Button>
          </div>
          <!-- Add buttons for filtering if needed -->
          <div class="input-group mt-3 shadow rounded">
            <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
              placeholder="Search Users..." aria-label="searchIcon" aria-describedby="searchIcon" />
          </div>
        </div>
      </div>
      <div class="w-75 table-responsive mt-2">
        <div v-if="users.data.length" class="d-flex justify-content-between mb-2">
          <div>
            <Link :href="route('observer.reports.collate')" class="btn btn-primary">All Technician Stats</Link>
          </div>
          <Pagination :links="users.links" :filter="filter" :key="'users'" />
        </div>
        <table class="table table-hover shadow custom-rounded-table">
          <thead v-if="!isLoading">
            <tr class="text-start">
              <th class="text-center text-muted">ID</th>
              <th class="text-muted">Name</th>
              <th class="text-muted">Email</th>
              <th class="text-muted">User Type</th>
              <th class="text-muted">Created At</th>
              <th v-if="filter.filterUsers === 'technician'" class="text-muted">Status</th>
            </tr>
          </thead>
          <tbody v-if="!isLoading">
            <tr v-for="user in users.data" :key="user.id" class="align-middle">
              <td class="text-center py-3">
                <Link :href="route('observer.users.show', user.id)" class="btn">
                {{ user.id }}
                </Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('observer.users.show', user.id)" class="btn">{{ user.name }}</Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('observer.users.show', user.id)" class="btn">{{ user.email }}</Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('observer.users.show', user.id)" class="btn">{{ user.user_type }}</Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('observer.users.show', user.id)" class="btn">{{ formatDate(user.created_at) }}</Link>
              </td>
              <td v-if="filter.filterUsers === 'technician'" class="text-start py-3">
                <Link v-if="user.technician" :href="route('observer.users.show', user.id)" class="btn">
                <span v-if="user.technician.is_working == 1" class="badge bg-success rounded-circle"
                  style="width: 2em; height: 2em;"><span class="visually-hidden">s</span></span>
                <span v-if="user.technician.is_working == 0" class="badge bg-danger rounded-circle"
                  style="width: 2em; height: 2em;"><span class="visually-hidden">s</span></span>
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Pagination from '@/Components/Pagination.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/ObserverHeader.vue";
import { Link, router, usePage } from '@inertiajs/vue3';
import Alpine from 'alpinejs';
import axios from "axios";
import moment from "moment";
import { defineProps, nextTick, reactive, ref, watch, watchEffect } from 'vue';

Alpine.start()

const page = usePage();

let showSuccessToast = ref(false);
let showErrorToast = ref(false);
let isImporting = ref(false); 

const handleBeforeUnload = (event) => {
  if (isImporting.value) {
    event.preventDefault();
    event.returnValue = ''; 
    return '';
  }
}

// Add event listener for beforeunload
window.addEventListener('beforeunload', handleBeforeUnload);

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

const isShowModal = ref(false);

function closeModal() {
  isShowModal.value = false;
  resetFileInput(); 
  file.value = null; 
  isLoading.value = false;
  removeBeforeUnloadListener();
}

function showModal() {
  isShowModal.value = true;
}

const props = defineProps({
  users: Object,
  filter: Object,
});

let search = ref(props.filter.search);
let sortColumn = ref("id");
let sortDirection = ref("asc");
let timeoutId = null;
let isLoading = ref(false);

// Load filter state from session

const fetchData = async (type, all, employee, technician) => {
  isLoading = true;
  await router.get(
    route('observer.users'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      filterUsers: type,
      all: all,
      employee: employee,
      technician: technician,
    },
    {
      preserveScroll: true,
      replace: true,
    }
  );
  // Update the filter state after fetching data
  /* filter.all = type === "all";
  filter.employee = type === "employee";
  filter.technician = type === "technician"; */

  router.remember({ filter: filter });

  await nextTick();
  console.log("After data fetch and filter update:", filter);
  isLoading = false;
}


const resetSorting = () => {
  sortColumn.value = "id";
  sortDirection.value = "asc";
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

const filters = reactive({
  all: true,
  employee: false,
  technician: false,
})

const filterUsers = async (type) => {
  console.log("Before filter change:", filters);
  if (type === "all") {
    filters.all = true;
    filters.employee = false;
    filters.technician = false;
  } else if (type === "employee") {
    filters.all = false;
    filters.employee = true;
    filters.technician = false;
  } else if (type === "technician") {
    filters.all = false;
    filters.employee = false;
    filters.technician = true;
  }
  await fetchData(type, filters.all, filters.employee, filters.technician);
  await nextTick();
  console.log("After filter change:", filters);
}

const file = ref(null);

const handleFileUpload = () => {
  file.value = document.querySelector('#file').files[0]
  console.log('after upload')
}

const resetFileInput = () => {
  file.value = null;
}

const removeBeforeUnloadListener = () => {
  window.removeEventListener('beforeunload', handleBeforeUnload);
}

const uploadCsv = async () => {
  if (!file.value) {
    return;
  }

  isImporting.value = true;
  isLoading.value = true;
  let formData = new FormData()
  formData.append('file', file.value)

  try {
    await new Promise(resolve => setTimeout(resolve, 2000));
    await axios.post(route('observer.users.bulk'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    console.log('File uploaded successfully')
    isShowModal.value = false
    fetchData()
  } catch (err) {
    console.log('Error uploading file:', err.response.data);
    console.error(err)
  } finally {
    isLoading.value = false;
    isImporting.value = false; 
    removeBeforeUnloadListener();
    resetFileInput(); 
  }
}

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
</script>

<style scoped>
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

.custom-modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  text-align: center;
  width: 25%;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
}
</style>
