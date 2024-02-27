<template>
  <div>
    <Header></Header>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <H1 class="fw-bold">View All Departments</H1>
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
            </tr>
          </thead>
          <tbody class="">
            <tr v-for="department in departments.data" :key="department.id">

              <td class="text-center py-4">{{ department.id }}</td>
              <td class="text-center" style="max-width: 60px;" @dblclick="startEditing(department.id, department.department)">
                <span v-if="selectedDepartmentId !== department.id">{{ department.department }}</span>
                <input v-model="editedDepartment[department.id]" v-if="selectedDepartmentId === department.id" @keyup.enter="saveDepartment(department.id)" @blur="saveDepartment(department.id)">

              </td>
              <!-- Date Created -->
              <td class="text-center">{{ formatDate(department.created_at) }}</td>
              <!-- Date Updated -->
              <td class="text-center">{{ formatDate(department.updated_at) }}</td>
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
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import Header from "@/Pages/Layouts/AdminHeader.vue";
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
//Start of Update Methods (In progress)
let editedDepartment = reactive({});
let selectedDepartmentId = ref(null);

let selectedRow = ref(null); 


const startEditing = (departmentId, departmentName) => {
  selectedDepartmentId.value = departmentId;
  if (!editedDepartment[departmentId]) {
    editedDepartment[departmentId] = departmentName;
  }
};

// Method to save the edited department name
const saveDepartment = async (departmentId) => {
    if (departmentId && editedDepartment[departmentId] !== null) {
        const form = useForm({
            department: editedDepartment[departmentId],
        });
        
        await form.put(route('admin.department.update', { department_id: departmentId }), {
            _method: 'put', // Specify the HTTP method as PUT
            department: editedDepartment[departmentId], // Pass the updated department data
        });
        
        // Reset the state
        selectedDepartmentId.value = null;
    }
};

</script>

<style scoped>

</style>