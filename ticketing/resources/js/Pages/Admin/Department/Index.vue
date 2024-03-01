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
          <h1 class="fw-bold">View All Departments</h1>
          <p class="fs-5"> Manage All Departments</p>

          <Link :href="route('admin.department.create')">
          <Button :name="'Add New Department'" :color="'primary'" class="btn btn-tickets btn-primary py-2 px-4"></Button>
          </Link>

        </div>
        <div class="input-group mt-3">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Department..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div class="w-75">
        <div v-if="departments.data.length" class="d-flex justify-content-end mb-2">
          <Pagination :links="departments.links" :key="'departments'" />
          <br>
        </div>
        <table class="table table-striped border border-secondary-subtle">
          <thead>
            <tr class="text-start">
              <th class="text-center"> Department ID</th>
              <th class="text-center">Department</th>
              <th class="text-center">Date Created</th>
              <th class="text-center">Date Updated</th>
              <th>Delete Option</th>
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="department in departments.data" :key="department.id">

              <td class="text-center py-4">{{ department.id }}</td>
              <td class="text-center" style="max-width: 60px;"
                @click="startEditing(department.id, department.department)">
                <span v-if="selectedDepartmentId !== department.id">{{ department.department }}</span>
                <input v-model="editedDepartment[department.id]" v-if="selectedDepartmentId === department.id"
                  @keyup.enter="saveDepartment(department.id)" @blur="saveDepartment(department.id)"
                  class="w-50 rounded border border-secondary-subtle text-center">
              </td>
              <td class="text-center">{{ formatDate(department.created_at) }}</td>
              <td class="text-center">{{ formatDate(department.updated_at) }}</td>
              <td><button type="button" as="button" class="btn btn-danger" @click="showDelete(department)">Delete</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <Delete v-if="isShowDelete" :department="selectedDepartmentId" @closeDelete="closeDelete"/>
  </div>
</template>

<script setup>
import Delete from "@/Components/DeleteModal.vue";
import Pagination from "@/Components/Pagination.vue";
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import moment from "moment";
import { reactive, ref, watchEffect } from 'vue';
import Toast from '@/Components/Toast.vue';
import Button from '@/Components/Button.vue';

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
  departments: Object,
});


//Start of Update Functions 
let editedDepartment = reactive({});
let selectedDepartmentId = ref(null);

const startEditing = (departmentId, departmentName) => {
  selectedDepartmentId.value = departmentId;
  if (!editedDepartment[departmentId]) {
    editedDepartment[departmentId] = departmentName;
  }
};

const saveDepartment = (departmentId) => {
  if (departmentId && editedDepartment[departmentId] !== null) {
    const form = useForm({
      department: editedDepartment[departmentId],
    });
    form.put(route('admin.department.update', { department_id: departmentId }));
    selectedDepartmentId.value = null;
  }
};
//End of Update Functions

const isShowDelete = ref(false);

function closeDelete() {
  if(isShowDelete.value) {
    selectedDepartmentId.value = null
    isShowDelete.value = false;
  }
}

function showDelete(department) {
  if(!isShowDelete.value) {
    selectedDepartmentId.value = department;
    isShowDelete.value = true;
  }
}

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
</script>

<style scoped></style>