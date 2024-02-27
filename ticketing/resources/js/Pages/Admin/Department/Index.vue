<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Departments</h1>
          <p class="fs-5"> Manage all Departments</p>

          <Link :href="route('admin.department.create')" class="btn btn-tickets btn-primary py-2 px-5">Add Department
          </Link>
          
          
        </div>
        <div class="input-group mt-3 mb-4">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Department..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

       <div class="w-75">
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
              <td class="text-center" style="max-width: 60px;" @dblclick="startEditing(department.id, department.department)">
                <span v-if="selectedDepartmentId !== department.id">{{ department.department }}</span>
                <input v-model="editedDepartment[department.id]" v-if="selectedDepartmentId === department.id" @keyup.enter="saveDepartment(department.id)" @blur="saveDepartment(department.id)">
              </td>
              <td class="text-center">{{ formatDate(department.created_at) }}</td>
              <td class="text-center">{{ formatDate(department.updated_at) }}</td>
              <td><button class="btn btn-danger" @click="showDeleteModal(department.id)">Delete</button></td>
            </tr>
          </tbody>
        </table>
        <div v-if="departments.data.length" class="flex justify-center w-full mt-6">
            <Pagination :links="departments.links" :key="'departments'"/>
            <br>
        </div>
      </div> 
    </div>
  </div>
  <DeleteModal v-if="isDeleteModalVisible"/>
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import Header from "@/Pages/Layouts/AdminHeader.vue";
import DeleteModal from "@/Components/DeleteModal.vue";
import moment from "moment";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, reactive} from 'vue'; 

const props = defineProps({
  departments: Object,
});

const search = ref('');

const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

//Start of Update Functions 
let editedDepartment = reactive({});
let selectedDepartmentId = ref(null);

const startEditing = (departmentId, departmentName) => {
  selectedDepartmentId.value = departmentId;
  if (!editedDepartment[departmentId]) {
    editedDepartment[departmentId] = departmentName;
  }
};

const saveDepartment = async (departmentId) => {
    if (departmentId && editedDepartment[departmentId] !== null) {
        const form = useForm({
            department: editedDepartment[departmentId],
        });
        await form.put(route('admin.department.update', { department_id: departmentId }), {
            _method: 'put',
            department: editedDepartment[departmentId],
        });
        selectedDepartmentId.value = null;
    }
};

//Start of Delete Functions
const isDeleteModalVisible = ref(false);
const selectedDepartmentForDeletion = ref(null);

const showDeleteModal = (departmentId) => {
  selectedDepartmentForDeletion.value = departmentId;
  console.log('selectedDepartmentForDeletion: ', selectedDepartmentForDeletion.value);
  isDeleteModalVisible.value = true; 
  console.log('isDeleteModalVisible: ', isDeleteModalVisible.value);
};

const deleteDepartment = async () => {
  isDeleteModalVisible.value = false; // Close the delete modal
  selectedDepartmentForDeletion.value = null; // Reset selected department ID
};

</script>

<style scoped>

</style>