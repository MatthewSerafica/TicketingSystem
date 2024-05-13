<template>
    <div>
      <Header class="sticky-top" style="z-index: 110;"></Header>
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
            <h1 class="fw-bold">View All Problems</h1>
            <p class="fs-5">Manage All Problems</p>
            <Link :href="route('admin.problems.create')">
              <Button :name="'Add New Problem'" :color="'primary'" class="btn btn-tickets btn-primary py-2 px-5"></Button>
            </Link>
          </div>
          <div class="input-group mt-3">
            <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
              placeholder="Search Problems..." aria-label="searchIcon" aria-describedby="searchIcon" />
          </div>
        </div>
  
        <div class="w-75">
          <div v-if="problems.data.length" class="d-flex justify-content-end mb-2">
            <pagination :links="problems.links" :key="'problems'" />
            <br>
          </div>
          <table class="table table-hover shadow custom-rounded-table">
            <thead>
              <tr class="text-start">
                <th class="text-center text-muted">Title ID</th>
                <th class="text-muted" style="width: 40%;">Titles</th>
                <th class="text-center text-muted">Date Created</th>
                <th class="text-center text-muted">Date Updated</th>
                <th class="text-muted">Delete Option</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="problem in problems.data" class="align-middle">
                <td class="text-center">{{ problem.id }}</td>
                <td style="max-width: 60px;" @dblclick="startEditing(problem.id, problem.problem)">
                  <span v-if="selectedProblemId !== problem.id">{{ problem.problem }}</span>
                  <input v-model="editedProblem[problem.id]" v-if="selectedProblemId === problem.id"
                    @keyup.enter="saveProblem(problem.id)" @blur="saveProblem(problem.id)">
                </td>
                <td class="text-center">{{ formatDate(problem.created_at) }}</td>
                <td class="text-center">{{ formatDate(problem.updated_at) }}</td>
                <td><button type="button" as="button" class="btn btn-danger" @click="showDelete(problem)">Delete</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <Delete v-if="isShowDelete" :problem="selectedProblemId" @closeDelete="closeDelete"/>
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
    problems: Object,
    filters: Object,
  });

  let search = ref(props.filters.search);
  let sortColumn = ref("id");
  let sortDirection = ref("asc");
  let timeoutId = null;
  
  const fetchData = () => {
  router.get(
    route('admin.problems'),
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
  
  
  let editedProblem = reactive({});
  let selectedProblemId = ref(null);
  
  const startEditing = (problemId, problemName) => {
    selectedProblemId.value = problemId;
    if (!editedProblem[problemId]) {
      editedProblem[problemId] = problemName;
    }
  };
  
  // Method to save the edited problem/title name
  const saveProblem = (problemId) => {
    if (problemId && editedProblem[problemId] !== null) {
      const form = useForm({
        problem: editedProblem[problemId],
      });
      form.put(route('admin.problems.update', { problem_id: problemId }));
      selectedProblemId.value = null;
    }
  };
  
  const isShowDelete = ref(false);
  
  function closeDelete() {
    if(isShowDelete.value) {
      selectedProblemId.value = null
      isShowDelete.value = false;
    }
  }
  
  function showDelete(problem) {
    if(!isShowDelete.value) {
      selectedProblemId.value = problem;
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