<template>
  <div>
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
              <label for="service" class="fw-semibold">Service</label>
              <select id="service" class="h-100 rounded border-secondary-subtle" placeholder="Select Service..."
                v-model="form.service">
                <option disabled>Select Service</option>
                <option value="Network Troubleshoot">Network Troubleshoot</option>
                <option value="Hardware Repair">Hardware Repair</option>
                <option value="Software Troubleshoot">Software Troubleshoot</option>
                <option value="Network Troubleshoot">Network Troubleshoot</option>
              </select>
            </div>
          </div>
          <div class="d-flex flex-row gap-5 justify-content-center">
            <div class="flex-shirnk-0">
              <label for="description" class="fw-semibold">Description</label>
              <input for="description" class="border-secondary-subtle" type="text"
                placeholder="Enter Ticket Description..." v-model="form.description" />
            </div>
          </div>
        </div>
        <div class="button-container">
          <button class="submit-ticket-button" type="submit" as="button">Submit</button>
          <button class="btn btn-link text-decoration-none" @click="cancel">Cancel</button>
          </div>
        <div class="table-container"></div>

      </div>
    </form>
  </div>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";

const page = usePage();

const form = useForm({
  issue: null,
  service: null,
  description: null,
  employee: page.props.user.id,
})
const create = () => {
    const url = route('employee.store');
    console.log('Generated URL:', url);
    form.post(url, { preserveScroll: false, preserveState: false });
};

const cancel = () => {
    const url = route('employee.index'); 
    console.log('Generated URL:', url);
    $inertia.visit(url);
};
</script>

<style>
/* Remove underline from Link component */
.text-decoration-none {
    text-decoration: none;
}
</style>