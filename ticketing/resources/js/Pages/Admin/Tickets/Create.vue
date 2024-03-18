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
                      placeholder="Enter Ticket Title..." v-model="form.issue" required />
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
                        <li v-else-if="!services">No results found...</li>
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
                              data-bs-toggle="dropdown" aria-expanded="false">
                              {{ dropdown.selectedTechnician ? dropdown.selectedTechnician : 'Assign a technician...'
                              }}
                            </button>
                            <button type="button"
                              class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                              data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                              <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                              <li v-for="technician in technicians" :key="technician.technician_id"
                                class="btn dropdown-item" @click="selectTechnician(technician, index)">
                                <span class="fw-semibold">{{ technician.user.name }}</span>
                                <br>
                                <small>{{ technician.assigned_department }}</small>
                              </li>
                              <li v-if="!technicians || technicians.length === 0">No technicians available</li>
                            </ul>
                          </div>
                          <button type="button"
                            class="btn border-0 rounded-pill d-flex justify-content-center align-items-center fs-6 text-danger"
                            @click="removeDropdown(index)">
                            <i class="bi bi-dash-circle"></i>
                          </button>
                        </div>
                      </div>
                      <div v-if="show">
                        <button type="button" as="button"
                          class="btn align-items-center justify-content-center d-flex text-primary fs-5 gap-3"
                          style="height:1.5em;" @click="addDropdown">
                          <i class="bi bi-plus-circle-fill"></i>
                          <span v-if="showLabel">Assign Technician</span>
                        </button>
                      </div>
                    </div>
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
import Toast from '@/Components/Toast.vue';
import Alpine from 'alpinejs';


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
  services: Object,
})

let selectedEmployee = ref('');
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
  techniciansData.value.splice(index, 1)
}

const selectTechnician = (technician, index) => {
  techniciansData.value[index].selectedTechnician = technician.user.name;
  techniciansData.value[index].technicianId = technician.technician_id;


  // Store the selected technicianId in the form.technicians array
  form.technicians.push(technician.technician_id);
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

  document.getElementById('employeeDropdown').classList.remove('show');
}
const selectService = (service) => {
  form.service = service.service;

  document.getElementById('technicianDropdown').classList.remove('show');
}

let show = ref(true);

const form = useForm({
  rs_no: null,
  issue: null,
  service: null,
  description: null,
  employee: null,
  technician1: null,
  technician2: null,
  technician3: null,
  technicians: [],
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