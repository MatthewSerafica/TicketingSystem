<template>
  <div>
    <Header></Header>
    <div class="mt-3">
      <form @submit.prevent="create">
        <br />
        <div class="container">
          <div class="title-container fw-bold mb-3 text-center">
            <h1 class="fw-bold">Create Tickets</h1>
          </div>

          <div class="create-ticket">
            <div class="row justify-content-center mb-4">
              <div class="">
                <div class="d-flex flex-row gap-3 w-50 justify-content-center mb-4">
                  <div class=" flex-shrink-1 w-25">
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

                  <div class=" flex-shrink-1 w-25">
                    <div class="d-flex flex-column flex-shrink-0">
                      <label for="rs_no" class="fw-semibold">RS No.</label>
                      <input id="rs_no" class="form-control h-100 rounded border-secondary-subtle" type="text"
                        placeholder="Enter RS No..." v-model="form.rs_no"
                        :disabled="form.request_type !== 'Requisition Slip'" />

                      <span v-if="form.errors.rs_no" class="error-message">{{ form.errors.rs_no }}</span>
                    </div>
                  </div>

                  <div class="flex-grow-1 w-50 d-flex flex-column">
                    <label for="Title" class="fw-semibold">Issue/Problem</label>
                    <div class="btn-group">
                      <button type="button" class="btn btn-outline-secondary text-start text-secondary-emphasis w-75"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ form.problem ? form.problem : 'Select an option...' }}
                      </button>
                      <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                        <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul id="titleDropdown" class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                        <li class="dropdown-item d-flex align-items-center">
                          <input type="text" class="form-control flex-grow-1" v-model="titleSearch"
                            placeholder="Search...">
                        </li>
                        <li class="dropdown-divider"></li>
                        <li v-if="filteredTitles.length === 0" class="dropdown-item">No titles found...</li>
                        <li v-else-if="filteredTitles" v-for="problem in filteredTitles" class="btn dropdown-item"
                          @click="selectProblem(problem)" style="width: 550px;">
                          <span class="fw-semibold">{{ problem.problem }}</span>
                        </li>
                        <li class="dropdown-item d-flex align-items-center">
                          <input type="text" class="form-control flex-grow-1 me-2" v-model="newProblem.problem"
                            placeholder="Enter new Problem" @keyup.enter.prevent="createNewProblem">
                          <button class="btn btn-primary" @click.prevent="createNewProblem">
                            <i class="bi bi-arrow-right"></i>
                          </button>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <div class="flex-grow-1 w-30">
                    <div class="d-flex flex-column flex-shrink-0">
                      <label for="complexity" class="fw-semibold">Complexity</label>
                      <select id="complexity" class="rounded border-secondary-subtle form-select"
                        placeholder="Select Complexity..." v-model="form.complexity">
                        <option disabled>Select Complexity</option>
                        <option value="Simple">Simple</option>
                        <option value="Complex">Complex</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="d-flex flex-row justify-content-center mb-4 w-75">
              <div class="col-md-8">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="description" class="fw-semibold">Description</label>
                  <textarea id="description" class="form-control border-secondary-subtle"
                    placeholder="Enter Ticket Description..." v-model="form.description" rows="5"></textarea>
                </div>
              </div>
            </div>
            <!-- employee -->
            <div class="d-flex flex-row mb-4 gap-4 w-50 align-items-center justify-content-center">
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
                  <ul id="employeeDropdown" class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                    <li class="px-2">
                      <input id="employee-search" class="form-control border-secondary-subtle" type="text"
                        placeholder="Search Employee..." v-model="employeeSearch" />
                    </li>
                    <li v-if="filteredEmployees.length === 0" class="dropdown-item">No results found...</li>
                    <li v-if="filteredEmployees" v-for="employee in filteredEmployees" class="btn dropdown-item"
                      @click="selectEmployee(employee)">
                      <span class="fw-semibold">{{ employee.user.name }}</span>
                      <br> <small>{{ employee.department }}-{{ employee.office }}</small>
                    </li>
                  </ul>
                </div>

              </div>
              <div class="flex-grow-1 w-50 d-flex flex-column">
                <label for="deptOffice" class="fw-semibold">Department & Office</label>
                <input id="deptOffice" class="form-control rounded border-secondary-subtle" type="text" placeholder=" "
                  v-model="form.department" disabled />
              </div>

              <div class="flex-grow-1 w-50">
                <div class="d-flex flex-column flex-shrink-0">
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
                      <li class="dropdown-item d-flex align-items-center">
                        <input type="text" class="form-control flex-grow-1" v-model="serviceSearch"
                          placeholder="Search Service...">
                      </li>
                      <li v-if="filteredServices.length === 0" class="dropdown-item">No services found...</li>
                      <li v-else-if="filteredServices" v-for="service in filteredServices" class="btn dropdown-item"
                        @click="selectService(service)" style="width: 550px;">
                        <span class="fw-semibold">{{ service.service }}</span>
                      </li>
                      <li class="dropdown-divider"></li>
                      <li class="dropdown-item d-flex align-items-center" >
                        <input type="text" class="form-control flex-grow-1 me-2" v-model="newService.service"
                          placeholder="Enter new Service" @keyup.enter="createNewService" > 
                        <button class="btn btn-primary ms-2" @click.prevent="createNewService">
                          <i class="bi bi-arrow-right"></i>
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-content-center">
              <div class="col gap-1">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="assignToSelf" v-model="form.assign_to_self">
                  <label class="form-check-label" for="assignToSelf">
                    Assign to Self
                  </label>
                </div>
              </div>
            </div>

            <div class="d-flex-row justify-content-end mb-4 w-50">
              <div class="d-flex justify-content-end gap-2">
                <Button :name="'Submit'" :color="'primary'" class="submit-btn"
                  :disabled="!form.request_type || !form.problem || !form.employee || !form.complexity || !form.request_type"></Button>
                <Link :href="`/technician/tickets`" class="btn btn-outline-secondary">Cancel</Link>
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
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from 'vue';

const props = defineProps({
  technicians: Object,
  employees: Object,
  problems: Object,
  filters: Object,
  new_rs: Object,
  services: Object,
})

const page = usePage()

const form = useForm({
  rs_no: props.new_rs,
  service: null,
  problem: null,
  description: null,
  complexity: null,
  employee: null,
  user: page.props.user.id,
  assign_to_self: true,
  request_type: null,
})

let selectedEmployee = ref('');
let selectedProblem = ref('');
let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;
let employeeSearch = ref('');
let titleSearch = ref('');
let serviceSearch = ref('');

const filteredEmployees = computed(() => {
  return props.employees.filter(employee => {
    return employee.user.name.toLowerCase().includes(employeeSearch.value.toLowerCase());
  });
});

const filteredTitles = computed(() => {
  return props.problems.filter(problem => {
    return problem.problem.toLowerCase().includes(titleSearch.value.toLowerCase());
  });
});

// Filtered service options based on search input
const filteredServices = computed(() => {
  return props.services.filter(service => {
    return service.service.toLowerCase().includes(serviceSearch.value.toLowerCase());
  });
});

const fetchData = () => {
  router.get(
    route('technician.tickets.create', {
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

const selectService = (service) => {
  form.service = service.service;

  document.getElementById('technicianDropdown').classList.remove('show');
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
  form.department = `${employee.department}-${employee.office}`;

  document.getElementById('employeeDropdown').classList.remove('show');
}

const selectProblem = (problem) => {
  selectedProblem.value = problem.problem;
  form.problem = problem.problem;

  document.getElementById('titleDropdown').classList.remove('show');
}

const create = () => {
  if (form.rs_no && !/^\d+$/.test(form.rs_no)) {
    form.errors.rs_no = 'Requisition Slip Number should contain only numeric characters.';
    return;
  }

  form.post(route('technician.tickets.store'), { preserveScroll: false, preserveState: false });
}

const newProblem = useForm({
  problem: null,
})

const createNewProblem = () => {
  newProblem.post(route('technician.ticket.problems.store'), { preserveScroll: false, preserveState: true });
  form.problem = newProblem.problem;
}

const newService = useForm({
  service: null,
})

const createNewService = () => {
  newService.post(route('technician.ticket.services.store'), { preserveScroll: false, preserveState: true });
  form.service = newService.service;
}
</script>

<style scoped>
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

.submit-btn {
  transition: all 0.2s;
}

.submit-btn:hover {
  transform: scale(1.1);
}

.error-message {
  color: red;
}
</style>
