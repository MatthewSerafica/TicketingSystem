<template>
  <Header></Header>
  <div class="mt-5 pt-5">
    <form @submit.prevent="create">
      <br />
      <div class="container">
        <div class="title-container">
          <h1>Create Tickets</h1>
        </div>

        <div class="create-ticket">
          <div class="d-flex flex-row gap-5 justify-content-center">
            <div class="flex-shrink-1">
              <label for="issue" class="fw-semibold">Title</label>
              <input id="issue" class="border-secondary-subtle" type="text" placeholder="Enter Ticket Title..."
                v-model="form.issue" />
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25">
              <label for="department" class="fw-semibold">Department/Office</label>
              <select id="department" class="h-100 rounded border-secondary-subtle" placeholder="Select Department..."
                v-model="form.department">
                <option disabled>Select Department</option>
                <option>Finance Department</option>
                <option>Registrar</option>
                <option>Help Desk</option>
              </select>
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25">
              <label for="service" class="fw-semibold">Employee</label>
              <select id="service" class="h-100 rounded border-secondary-subtle" placeholder="Assign Technician..."
                v-model.number="form.employee">
                <option disabled>Assign Employee</option>
                <option v-for="employee in employees" :value="employee.employee_id">{{ employee.user.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="d-flex flex-row gap-5 justify-content-center">
            <div class="flex-shirnk-0">
              <label for="description" class="fw-semibold">Description</label>
              <input for="description" class="border-secondary-subtle" type="text"
                placeholder="Enter Ticket Description..." v-model="form.description" />
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25">
              <label for="service" class="fw-semibold">Service</label>
              <select id="service" class="h-100 rounded border-secondary-subtle" placeholder="Select Service..."
                v-model="form.service">
                <option disabled>Select Service</option>
                <option>Network Troubleshoot</option>
                <option>Hardware Repair</option>
                <option>Software Troubleshoot</option>
                <option>Network Troubleshoot</option>
              </select>
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25">
              <label for="service" class="fw-semibold">Technicians</label>
              <select id="service" class="h-100 rounded border-secondary-subtle" placeholder="Assign Technician..."
                v-model.number="form.technician">
                <option disabled>Assign Technician</option>
                <option v-for="technician in technicians" :value="technician.technician_id">{{ technician.user.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
        <div class="button-container">
          <button class="submit-ticket-button" type="submit" as="button">Submit</button>
          <Link :href="`/admin/tickets`" class="create-ticket-link">Cancel</Link>
        </div>

        <div class="table-container"></div>

      </div>
    </form>
  </div>
</template>
  
<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
  technicians: Object,
  employees: Object,
})

const form = useForm({
  issue: null,
  service: null,
  description: null,
  employee: null,
  technician: null,
})

const create = () => form.post(route('admin.tickets.store'), { preserveScroll: false, preserveState: false })
</script>
  
<style>
.title-container {
  text-align: center;
}

.create-ticket {
  margin: 10px 0;
  display: flex;
  align-items: center;
  flex-direction: column;
}

.create-ticket div {
  margin-bottom: 20px;
  width: 80%;
}

.create-ticket input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.container {
  padding: 20px;
}

h1 {
  font-size: 36px;
  margin-bottom: 10px;
}

p {
  justify-content: start;
  font-size: 16px;
  margin-bottom: 10px;
}

.button-container {
  display: flex;
  justify-content: center;
}

.submit-ticket-button {
  width: 10%;
  margin-right: 10px;
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  background-color: #000066;
  color: #fff;
  border-radius: 8px;
  /* Adjust border-radius for rounded edges */
  cursor: pointer;
  transition: background-color 0.3s;
}
</style>
  