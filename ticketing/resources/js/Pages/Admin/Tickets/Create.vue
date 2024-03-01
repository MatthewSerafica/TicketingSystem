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
                    <input id="rs_no" class="form-control rounded border-secondary-subtle" type="text" placeholder="Enter RS No..." v-model="form.rs_no" />
                  </div>
                  <div class="flex-grow-1 w-50">
                    <label for="issue" class="fw-semibold">Title</label>
                    <input id="issue" class="form-control rounded border-secondary-subtle" type="text" placeholder="Enter Ticket Title..." v-model="form.issue" />
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <div class="d-flex flex-row gap-3">
                  <div class="flex-grow-1 w-50">
                    <label for="department" class="fw-semibold">Department/Office</label>
                    <select id="department" class="form-select rounded border-secondary-subtle" placeholder="Select Department..." v-model="form.department">
                      <option disabled>Select Department</option>
                      <option>Finance Department</option>
                      <option>Registrar</option>
                      <option>Help Desk</option>
                    </select>
                  </div>
                  <div class="flex-grow-1 w-50">
                    <label for="service" class="fw-semibold">Employee</label>
                    <select id="service" class="form-select rounded border-secondary-subtle" placeholder="Assign Technician..." v-model.number="form.employee">
                      <option disabled>Assign Employee</option>
                      <option v-for="employee in employees" :value="employee.employee_id">{{ employee.user.name }}</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <label for="description" class="fw-semibold">Description</label>
                <textarea id="description" class="form-control rounded border-secondary-subtle p-3" placeholder="Enter Ticket Description..." v-model="form.description" rows="5"></textarea>
              </div>
            </div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <div class="d-flex flex-row gap-3">
                  <div class="flex-grow-1 w-50">
                    <label for="service" class="fw-semibold">Service</label>
                    <select id="service" class="form-select rounded border-secondary-subtle" placeholder="Select Service..." v-model="form.service">
                      <option disabled>Select Service</option>
                      <option value="Network Troubleshoot">Network Troubleshoot</option>
                      <option value="Hardware Repair">Hardware Repair</option>
                      <option value="Software Troubleshoot">Software Troubleshoot</option>
                    </select>
                  </div>
                  <div class="flex-grow-1 w-50">
                    <label for="technician" class="fw-semibold">Technicians</label>
                    <select id="technician" class="form-select rounded border-secondary-subtle" placeholder="Assign Technician..." v-model.number="form.technician">
                      <option disabled>Assign Technician</option>
                      <option v-for="technician in technicians" :value="technician.technician_id">{{ technician.user.name }}</option>
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
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, useForm } from "@inertiajs/vue3";
import Button from '@/Components/Button.vue';

const props = defineProps({
  technicians: Object,
  employees: Object,
})

const form = useForm({
  rs_no: null,
  issue: null,
  service: null,
  description: null,
  employee: null,
  technician: null,
})

const create = () => form.post(route('admin.tickets.store'), { preserveScroll: false, preserveState: false })
</script>
  
<style>
</style>
  