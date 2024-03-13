<template>
  <div>
    <Header></Header>
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
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Users</h1>
          <p class="fs-5">Manage and Track All Users</p>
          <div class="d-flex flex-row gap-2 justify-content-center align-items-center w-100">
            <Link :href="route('admin.users.create')"
              class="btn btn-tickets btn-primary w-50 d-flex justify-content-center align-items-center">
            Create User
            </Link>
            <Button :name="'Import'" :color="'outline-primary'" :width="'50'" @click="showModal"></Button>
            <div v-if="isShowModal" class="custom-modal">
              <div class="modal-content d-flex flex-column">
                <div class="d-flex flex-row justify-content-end">
                  <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div>
                  <form @submit.prevent="uploadCsv" enctype="multipart/form-data">
                    <div class="d-flex flex-column justify-content-center align-items-start">
                      <label for="file" class="form-label fw-semibold">Upload File</label>
                      <input type="file" id="file" @change="handleFileUpload" class="form-control" accept=".csv">
                    </div>
                    <div class="d-flex flex-row gap-2 justify-content-end mt-3">
                      <button type="submit" class="btn btn-primary">
                        Submit</button>
                      <button @click="closeModal" type="button" class="btn btn-secondary">Cancel</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex flex-row justify-content-center align-items-center gap-3 mt-2">
            <Button :name="'All'" :color="'secondary'" class="btn-options" @click="filterUsers('all')"></Button>
            <Button :name="'Employees'" :color="'secondary'" class="btn-options"
              @click="filterUsers('employee')"></Button>
            <Button :name="'Technicians'" :color="'secondary'" class="btn-options"
              @click="filterUsers('technician')"></Button>
          </div>
          <!-- Add buttons for filtering if needed -->
          <div class="input-group mt-3">
            <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
              placeholder="Search Users..." aria-label="searchIcon" aria-describedby="searchIcon" />
          </div>
        </div>
      </div>

      <div class="w-75">
        <div v-if="users.data.length" class="d-flex justify-content-end mb-2">
          <Pagination :links="users.links" :key="'users'" />
        </div>
        <table class="table table-hover shadow custom-rounded-table">
          <thead v-if="!isLoading">
            <tr class="text-start">
              <th class="text-center text-muted">ID</th>
              <th class="text-muted">Name</th>
              <th class="text-muted">Email</th>
              <th class="text-muted">User Type</th>
              <th class="text-muted">Created At</th>
            </tr>
          </thead>
          <tbody v-if="!isLoading">
            <tr v-for="user in users.data" :key="user.id" class="align-middle">
              <td class="text-center py-3">
                <Link :href="route('admin.users.show', user.id)" class="btn">
                {{ user.id }}
                </Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('admin.users.show', user.id)" class="btn">{{ user.name }}</Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('admin.users.show', user.id)" class="btn">{{ user.email }}</Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('admin.users.show', user.id)" class="btn">{{ user.user_type }}</Link>
              </td>
              <td class="text-start py-3">
                <Link :href="route('admin.users.show', user.id)" class="btn">{{ formatDate(user.created_at) }}</Link>
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
import Header from "@/Pages/Layouts/AdminHeader.vue";
import Toast from '@/Components/Toast.vue';
import axios from "axios"
import { router, Link, usePage } from '@inertiajs/vue3';
import moment from "moment";
import Alpine from 'alpinejs';
import { computed, defineProps, nextTick, reactive, ref, watch, watchEffect } from 'vue';

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

const isShowModal = ref(false);

function closeModal() {
  isShowModal.value = false;
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

const fetchData = async (type) => {
  isLoading = true;
  await router.get(
    route('admin.users'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
      filterUsers: type,
    },
    {
      preserveScroll: true,
      replace: true,
    }
  );
  // Update the filter state after fetching data
  filter.all = type === "all";
  filter.employee = type === "employee";
  filter.technician = type === "technician";
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

const filter = reactive({
  all: true,
  employee: false,
  technician: false,
})

const filterUsers = async (type) => {
  console.log("Before filter change:", filter);
  if (type === "all") {
    filter.all = true;
    filter.employee = false;
    filter.technician = false;
  } else if (type === "employee") {
    filter.all = false;
    filter.employee = true;
    filter.technician = false;
  } else if (type === "technician") {
    filter.all = false;
    filter.employee = false;
    filter.technician = true;
  }
  await fetchData(type); // Pass the type to fetchData and wait for it to complete
  // Use nextTick to log the updated state after the next DOM update
  await nextTick();
  console.log("After filter change:", filter);
}

const file = ref(null);

const handleFileUpload = () => {
  file.value = document.querySelector('#file').files[0]
  console.log('after upload')
}

const uploadCsv = async () => {
  let formData = new FormData()
  console.log('formData:', formData); 
  formData.append('file', file.value)

  try {
    await axios.post(route('admin.users.bulk'), formData, {
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
