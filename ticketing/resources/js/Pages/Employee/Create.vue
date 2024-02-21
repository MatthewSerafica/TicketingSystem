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
          <Link :href="`/admin/tickets`" class="create-ticket-link">Cancel</Link>
        </div>

        <div class="table-container"></div>

      </div>
    </form>
  </div>
  </div>
</template>
  
<script setup>
import Header from "@/Pages/Layouts/EmployeeHeader.vue";
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
  