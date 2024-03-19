<template>
  <div>
    <Header></Header>
    <div class="mt-2 pt-5">
      <form @submit.prevent="create">
        <br />
        <div class="container">
          <div class="title-container fw-bold mb-5 text-center">
            <h1>Create Tickets</h1>
          </div>

          <div class="create-ticket d-flex flex-column justify-content-center align-items-center">
            <div class="d-flex flex-row gap-3 w-50 justify-content-center mb-4">
              <div class=" flex-grow-1 w-50">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="rs_no" class="fw-semibold">Requisition Slip No.</label>
                  <input id="rs_no" class="form-control h-100 rounded border-secondary-subtle" type="text"
                    placeholder="Enter RS No..." v-model="form.rs_no" />
                  <span v-if="form.errors.rs_no" class="error-message">{{ form.errors.rs_no }}</span>
                </div>
              </div>
              <div class="flex-grow-1 w-50">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="issue" class="fw-semibold">Title</label>
                  <input id="issue" class="form-control h-100 border-secondary-subtle" type="text"
                    placeholder="Enter Ticket Title..." v-model="form.issue" required />
                </div>
              </div>
            </div>

            <div class="d-flex flex-row justify-content-center mb-4 w-75">
              <div class="col-md-8">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="description" class="fw-semibold">Description</label>
                  <textarea id="description" class="form-control border-secondary-subtle"
                    placeholder="Enter Ticket Description..." v-model="form.description" rows="5" required></textarea>
                </div>
              </div>
            </div>

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
              <div class="flex-grow-1 w-50">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="service" class="fw-semibold">Service</label>
                  <select id="service" class="h-100 rounded border-secondary-subtle form-select"
                    placeholder="Select Service..." v-model="form.service">
                    <option disabled>Select Service</option>
                    <option value="Network Troubleshoot">Network Troubleshoot</option>
                    <option value="Hardware Repair">Hardware Repair</option>
                    <option value="Software Troubleshoot">Software Troubleshoot</option>
                  </select>
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
                <Button :name="'Submit'" :color="'primary'" class="submit-btn"></Button>
                <Link :href="`/technician/tickets`" class="btn btn-outline-primary">Cancel</Link>
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
import { ref, watch } from 'vue';

const props = defineProps({
  technicians: Object,
  employees: Object,
  filters: Object,
})

const page = usePage()

const form = useForm({
  rs_no: null,
  issue: null,
  service: null,
  description: null,
  employee: null,
  user: page.props.user.id,
  assign_to_self: false,
})

let selectedEmployee = ref('');
let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;

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

  document.getElementById('employeeDropdown').classList.remove('show');
}

const create = () => {
  if (form.rs_no && !/^\d+$/.test(form.rs_no)) {
    form.errors.rs_no = 'Requisition Slip Number should contain only numeric characters.';
    return;
  }

  form.post(route('technician.tickets.store'), { preserveScroll: false, preserveState: false });
}
</script>

<style scoped>
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
