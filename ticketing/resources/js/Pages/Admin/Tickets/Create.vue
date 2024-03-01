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

          <div class="create-ticket">

            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <div class="d-flex flex-row gap-3">
                  <div class="flex-grow-1 w-50">
                    <label for="rs_no" class="fw-semibold">Requisition Slip No.</label>
                    <input id="rs_no" class="form-control rounded border-secondary-subtle" type="text"
                      placeholder="Enter RS No..." v-model="form.rs_no" />
                    <span v-if="form.errors.rs_no" class="error-message">{{ form.errors.rs_no }}</span>
                  </div>

                  <div class="flex-grow-1 w-50">
                    <label for="issue" class="fw-semibold">Title</label>
                    <input id="issue" class="form-control rounded border-secondary-subtle" type="text"
                      placeholder="Enter Ticket Title..." v-model="form.issue" />
                  </div>

                  <div class="flex-grow-1 w-50 d-flex flex-column">
                    <label for="employee" class="fw-semibold">Employee</label>
                    <div class="btn-group" @click.stop>
                      <button type="button" class="btn btn-outline-secondary text-start text-secondary-emphasis w-75"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ selectedEmployee ? selectedEmployee : 'Select a client...' }}
                      </button>
                      <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                        <span class="visually-hidden">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="px-2">
                          <input id="employee-search" class="form-control border-secondary-subtle" type="text"
                            placeholder="Search Employee..." v-model="search"/>
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
                  <div class="flex-grow-1 w-50">
                    <label for="service" class="fw-semibold">Service</label>
                    <select id="service" class="form-select rounded border-secondary-subtle"
                      placeholder="Select Service..." v-model="form.service">
                      <option disabled>Select Service</option>
                      <option value="Network Troubleshoot">Network Troubleshoot</option>
                      <option value="Hardware Repair">Hardware Repair</option>
                      <option value="Software Troubleshoot">Software Troubleshoot</option>
                    </select>
                  </div>
                  <div class="flex-grow-1 w-50">
                    <label for="technician" class="fw-semibold">Technicians</label>
                    <select id="technician" class="form-select rounded border-secondary-subtle"
                      placeholder="Assign Technician..." v-model.number="form.technician">
                      <option disabled>Assign Technician</option>
                      <option v-for="technician in technicians" :value="technician.technician_id">{{ technician.user.name
                      }}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="d-flex justify-content-end gap-2">
                <Button :name="'Submit'" :color="'primary'"></Button>
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
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
  technicians: Object,
  employees: Object,
  filters: Object,
})

let selectedEmployee = ref('');
let search = ref(props.filters.search);
let sortColumn = ref("ticket_number");
let sortDirection = ref("asc");
let timeoutId = null;

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
  if (!search.value) {
    resetSorting()
  }
  debouncedFetchData();
})

const selectEmployee = (employee) => {
  selectedEmployee.value = employee.user.name;
  form.employee = employee.employee_id;
}

const form = useForm({
  rs_no: null,
  issue: null,
  service: null,
  description: null,
  employee: null,
  technician: null,
})

const create = () => {
  if (form.rs_no && !/^\d+$/.test(form.rs_no)) {
    form.errors.rs_no = 'Requisition Slip Number should contain only numeric characters.';
    return;
  }

  form.post(route('admin.tickets.store'), { preserveScroll: false, preserveState: false });
}
</script>
  
<style scoped>
.error-message {
  color: red;
}
</style>
  