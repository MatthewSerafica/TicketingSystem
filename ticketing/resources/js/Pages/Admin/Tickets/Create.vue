<template>
  <div>
    <Header></Header>
    <!--Toast Render-->
    <div class="position-absolute end-0 mt-3 me-3" style="z-index: 100;">
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
    <div class="mt-3">
      <form @submit.prevent="create">
        <br />
        <div class="container">
          <div class="title-container fw-bold mb-3 text-center">
            <h1 class="fw-bold">Create Tickets</h1>
          </div>

          <div class="create-ticket">
            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <div class="d-flex flex-row gap-3 mb-2">
                  <div class="flex-grow-1 w-50">
                    <div class="flex-grow-1 w-80 d-flex flex-column">
                      <label for="request_type" class="fw-semibold">Request Type</label>
                      <select id="request_type" class="h-100 rounded border-secondary-subtle form-select"
                        placeholder="Select Request Type..." v-model="form.request_type" @change="toggleRSNoField">
                        <option disabled>Select Type</option>
                        <option value="Requisition Slip">RS</option>
                        <option value="Phone Call">Phone Call</option>
                        <option value="Text">Text</option>
                        <option value="Verbal">Verbal</option>
                      </select>
                    </div>
                  </div>

                  <div class="flex-grow-1 w-50">
                    <label for="rs_no" class="fw-semibold">Requisition Slip No.</label>
                    <input id="rs_no" class="form-control rounded border-secondary-subtle" type="text"
                      placeholder="Enter RS No..." v-model="form.rs_no" :disabled="form.request_type !== 'Requisition Slip'"/>
                    <span v-if="form.errors.rs_no" class="error-message">{{ form.errors.rs_no }}</span>
                  </div>

                </div>
                <div class="d-flex flex-row gap-3">
                  <div class="flex-grow-1 w-50 d-flex flex-column">
                    <label for="Title" class="fw-semibold">Title</label>
                    <div class="btn-group">
                      <button type="button" class="btn btn-outline-secondary text-start text-secondary-emphasis w-75"
                              data-bs-toggle="dropdown" aria-expanded="false">
                          {{ form.problem ? form.problem : 'Select a Title...' }}
                      </button>
                      <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                              data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                          <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul id="titleDropdown" class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                          <li v-if="problems" v-for="problem in problems" class="btn dropdown-item"
                              @click="selectProblem(problem)" style="width: 400px;">
                              <span class="fw-semibold">{{ problem.problem }}</span>
                          </li>
                          <li class="dropdown-divider"></li>
                          <li class="dropdown-item">
                              <input type="text" class="form-control" v-model="newProblem.problem" placeholder="Enter custom problem"
                                    @keyup.enter="createNewProblem">
                          </li>
                      </ul>
                  </div>


                  </div>

                  <div class="flex-grow-1 w-50 d-flex flex-column">
                    <label for="employee" class="fw-semibold">Employee</label>
                    <div class="btn-group">
                      <button type="button" class="btn btn-outline-secondary text-start text-secondary-emphasis w-75"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ selectedEmployee ? selectedEmployee : 'Select a client...' }}
                      </button>
                      <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                        <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul id="employeeDropdown" class="dropdown-menu" :class="{ 'show': search }"
                        style="max-height: 300px; overflow-y: auto;">
                        <li class="px-2">
                          <input id="employee-search" class="form-control border-secondary-subtle" type="text"
                            placeholder="Search Employee..." v-model="search" />
                        </li>
                        <li v-if="employees" v-for="employee in employees" class="btn dropdown-item"
                          @click="selectEmployee(employee)">
                          <span class="fw-semibold">{{ employee.user.name }}</span>
                          <br> <small>{{ employee.department }}-{{ employee.office }}</small>
                        </li>
                        <li v-else-if="!employees">No results found...</li>
                      </ul>
                    </div>
                  </div>

                  <div class="flex-grow-1 w-50 d-flex flex-column">
                    <label for="deptOffice" class="fw-semibold">Department & Office</label>
                    <input id="deptOffice" class="form-control rounded border-secondary-subtle" type="text"
                      placeholder=" " v-model="form.department_office" disabled />
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <label for="description" class="fw-semibold">Description</label>
                <textarea id="description" class="form-control rounded border-secondary-subtle p-3"
                  placeholder="Enter Ticket Description..." v-model="form.description" rows="5"></textarea>
              </div>
            </div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <div class="d-flex flex-row gap-3">
                  <div class="flex-grow-1 w-50 d-flex flex-column">
                    <label for="service" class="fw-semibold">Service</label>
                    <div class="btn-group">
                      <button type="button" class="btn btn-outline-secondary text-start text-secondary-emphasis w-75"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ form.service ? form.service : 'Select a service...' }}
                      </button>
                      <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                        <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul id="serviceDropdown" class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                        <li v-if="services" v-for="service in services" class="btn dropdown-item"
                          @click="selectService(service)" style="width: 400px;">
                          <span class="fw-semibold">{{ service.service }}</span>
                        </li>
                        <li class="dropdown-divider"></li>
                          <li class="dropdown-item">
                          <input type="text" class="form-control" v-model="form.otherService" placeholder="Enter custom service">
                          </li>
                      </ul>
                    </div>
                  </div>
                  <div class="w-50 d-flex flex-column justify-content-start align-items-start">
                    <label for="" class="fw-semibold">Technicians</label>
                    <div class="d-flex flex-column justify-content-center align-items-center gap-2 w-100">
                      <div v-for="(dropdown, index) in techniciansData" :key="index">
                        <div class="flex-grow-1 w-100 d-flex flex-row gap-2">
                          <div class="btn-group">
                            <button type="button"
                              class="btn btn-outline-secondary text-start text-secondary-emphasis w-100"
                              aria-expanded="false">
                              {{ dropdown.selectedTechnician ? dropdown.selectedTechnician : 'Assign a technician...'
                              }}
                            </button>
                            <button type="button"
                              class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                              data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent"
                              @click="fetchRecommended(form.department)">
                              <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                              <div>
                                <h6 class="dropdown-header">Recommended</h6>
                                <div v-for="technicians in recommended">
                                  <li v-for="technician in technicians" class="btn dropdown-item"
                                    @click="selectTechnician(technician, index)"
                                    :class="{ 'disabled': technician.tickets_assigned >= 5 }">
                                    <div class="d-flex justify-content-between">
                                      <div>
                                        <span class="fw-semibold">{{ technician.user.name }}</span>
                                        <br> <small>{{ technician.assigned_department }}</small>
                                      </div>
                                      <span>{{ technician.tickets_assigned }}</span>
                                    </div>
                                  </li>
                                </div>
                              </div>
                              <li>
                                <hr class="dropdown-divider">
                              </li>
                              <div>
                                <h6 class="dropdown-header">All</h6>
                                <li v-for="technician in technicians" class="btn dropdown-item"
                                  @click="selectTechnician(technician, index)"
                                  :class="{ 'disabled': technician.tickets_assigned >= 5 }">
                                  <div class="d-flex justify-content-between">
                                    <div>
                                      <span class="fw-semibold">{{ technician.user.name }}</span>
                                      <br> <small>{{ technician.assigned_department }}</small>
                                    </div>
                                    <span>{{ technician.tickets_assigned }}</span>
                                  </div>
                                </li>
                                <li v-if="!technicians || technicians.length === 0">No technicians available</li>
                              </div>
                            </ul>
                          </div>
                          <button type="button"
                            class="btn border-0 rounded-pill d-flex justify-content-center align-items-center fs-5 text-danger"
                            @click="removeDropdown(index)">
                            <i class="bi bi-dash-circle-fill"></i>
                          </button>
                        </div>
                      </div>
                      <div v-if="show">
                        <button type="button" as="button"
                          class="btn align-items-center justify-content-center d-flex text-primary fs-5 gap-2"
                          style="height:1.5em;" @click="addDropdown">
                          <i class="bi bi-plus-circle-fill"></i>
                          <span v-if="showLabel">Assign Technician</span>
                        </button>
                      </div>
                      <span v-if="form.errors.technician" class="error-message">{{ form.errors.technician }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="d-flex justify-content-end gap-2">
                <Button :name="'Submit'" :color="'primary'"
                  :disabled="!form.problem || !form.employee || !form.description || !form.service || !form.technicians.length"></Button>
                <Link :href="`/admin/tickets`" class="btn btn-outline-primary">Cancel</Link>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import axios from 'axios';
import { ref, watch, watchEffect } from "vue";


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
  technicians: Object,
  employees: Object,
  filters: Object,
  problems: Object,
  services: Object,
  new_rs: Object,
})

let selectedEmployee = ref('');
let selectedProblem = ref('');
let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;
let techniciansData = ref([]);
let showLabel = ref(true);

const addDropdown = () => {
  techniciansData.value.push({
    selectedTechnician: ref(''),
    technicianId: null,
  })
}

const removeDropdown = (index) => {
  const removedTechnicianId = techniciansData.value[index].technicianId;
  techniciansData.value.splice(index, 1);

  // Remove the technicianId from the form.technicians array
  const technicianIndex = form.technicians.indexOf(removedTechnicianId);
  if (technicianIndex !== -1) {
    form.technicians.splice(technicianIndex, 1);
  }
}

const selectTechnician = (technician, index) => {
  if (form.technicians.includes(technician.technician_id)) {
    form.errors.technician = 'Technician is already selected.';
    return;
  }

  if (techniciansData.value.some(item => item.technicianId === technician.technician_id)) {
    form.errors.technician = 'Technician is already selected.';
    return;
  }

  techniciansData.value[index].selectedTechnician = technician.user.name;
  techniciansData.value[index].technicianId = technician.technician_id;

  form.technicians.push(technician.technician_id);

  form.errors.technician = null;
};


const fetchData = () => {
  router.get(
    route('admin.tickets.create', {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
    },
      {
        preserveState: true,
        replace: true,
        preserveScroll: true,
      }
    )
  )
}
const resetSorting = () => {
  console.log("Reset Sorting");
  sortColumn.value = "employee_id"
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
  if (search.value === '') {
    resetSorting();
  }
  debouncedFetchData();
})


const selectEmployee = (employee) => {
  selectedEmployee.value = employee.user.name;
  form.employee = employee.employee_id;

  form.department_office = `${employee.department}-${employee.office}`;
  form.department = `${employee.department}`;

  document.getElementById('employeeDropdown').classList.remove('show');
}

const selectService = (service) => {
  form.service = service.service;

  document.getElementById('technicianDropdown').classList.remove('show');
}

const selectProblem = (problem) => {
  selectedProblem.value = problem.problem;
  form.problem = problem.problem;

  document.getElementById('titleDropdown').classList.remove('show');
}

let recommended = ref([]);

const fetchRecommended = async (department) => {
  try {
    const response = await axios.get(route('admin.recommended', department))

    if (response.status !== 200) {
      throw new Error('Failed to fetch recommended technicians');
    }

    recommended.value = response.data;


  } catch (error) {
    console.error('Error fetching recommended technicians:', error);
    return null;
  }
}

const toggleRSNoField = () => {
  if (form.request_type === 'Requisition Slip') {
    form.rs_no = props.new_rs; 
    document.getElementById('rs_no').disabled = false;
  } else {
    form.rs_no = null;
    document.getElementById('rs_no').disabled = true;
  }
}


let show = ref(true);

const form = useForm({
  request_type: null,
  rs_no: props.new_rs,
  service: null,
  problem: null,
  description: null,
  employee: null,
  technicians: [],
})

const create = () => {
  if (form.rs_no && !/^\d+$/.test(form.rs_no)) {
    form.errors.rs_no = 'Requisition Slip Number should contain only numeric characters.';
    return;
  }

  form.post(route('admin.tickets.store'), { preserveScroll: false, preserveState: false });
}

const newProblem = useForm({
  problem: null,
})

const createNewProblem = () => {
  newProblem.post(route('admin.ticket.problems.store'), {preserveScroll:false, preserveState:true});
  form.problem = newProblem.problem;
}


</script>

<style scoped>
/* Transition for the dropdown Menus 6*/
.dropdown-menu {
  display: none;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.dropdown-menu.show {
  display: block;
  opacity: 1;
}

.dropdown-item {
  opacity: 0;
  transition: opacity 0.5s ease;
}

.dropdown-menu.show .dropdown-item {
  opacity: 1;
}

.dropdown-item {
  animation: fadeIn 0.5s ease forwards;
}

@keyframes fadeIn {
  0% {
    opacity: 0;
  }

  100% {
    opacity: 1;
  }
}


.error-message {
  color: red;
}

.technicians {
  width: 28.5rem;
}

.technician {
  width: 100%;
}

.droppy {
  width: 5.1rem;
}

@media (max-width: 1024px) {
  .technicians {
    width: 21rem;
  }

  .droppy {
    width: 4.1rem;
  }
}

@media (max-width: 768px) {
  .technicians {
    width: 16rem;
  }

  .droppy {
    width: 3.4rem;
  }
}

@media (max-width: 768px) {
  .technicians {
    width: 16rem;
  }

  .droppy {
    width: 3.4rem;
  }
}

@media (max-width: 426px) {
  .technicians {
    width: 10rem;
  }

  .technician {
    width: 8rem;
  }

  .droppy {
    height: 3.85rem;
  }
}
</style>