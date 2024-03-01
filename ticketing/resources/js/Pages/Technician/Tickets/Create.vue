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
              <div class="col-md-4">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="rs_no" class="fw-semibold">Requisition Slip No.</label>
                  <input id="rs_no" class="form-control h-100 rounded border-secondary-subtle" type="text" placeholder="Enter RS No..." v-model="form.rs_no" />
                  <span v-if="form.errors.rs_no" class="error-message">{{ form.errors.rs_no }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="issue" class="fw-semibold">Title</label>
                  <input id="issue" class="form-control h-100 border-secondary-subtle" type="text" placeholder="Enter Ticket Title..."
                    v-model="form.issue" required/>
                </div>
              </div>
            </div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="description" class="fw-semibold">Description</label>
                  <textarea id="description" class="form-control border-secondary-subtle" placeholder="Enter Ticket Description..." v-model="form.description" rows="5" required></textarea>
                </div>
              </div>
            </div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-4">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="employee" class="fw-semibold">Employee</label>
                  <select id="employee" class="h-100 rounded border-secondary-subtle form-select" placeholder="Assign Employee..."
                    v-model.number="form.employee">
                    <option disabled>Assign Employee</option>
                    <option v-for="employee in employees" :value="employee.employee_id">{{ employee.user.name }}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex flex-column flex-shrink-0">
                  <label for="service" class="fw-semibold">Service</label>
                  <select id="service" class="h-100 rounded border-secondary-subtle form-select" placeholder="Select Service..."
                    v-model="form.service">
                    <option disabled>Select Service</option>
                    <option value="Network Troubleshoot">Network Troubleshoot</option>
                    <option value="Hardware Repair">Hardware Repair</option>
                    <option value="Software Troubleshoot">Software Troubleshoot</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="row justify-content-center mb-4">
              <div class="col-md-8 d-flex justify-content-end gap-2">
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
import { Link, useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
  technicians: Object,
  employees: Object,
})

const page = usePage()

const form = useForm({
  rs_no: null,
  issue: null,
  service: null,
  description: null,
  employee: null,
  technician: page.props.user.id,
})

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
