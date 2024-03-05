<template>
  <div>
    <Header></Header>
    <div class="mt-5 pt-5">
      <form @submit.prevent="create">
        <br />
        <div class="container">
          <div class="title-container text-center">
            <h1>Create Tickets</h1>
          </div>

          <div class="create-ticket mt-2">
            <div class="d-flex flex-row gap-5 justify-content-center mb-4">
              <div class="d-flex flex-column flex-shrink-0 w-25">
                <label for="issue" class="fw-semibold">Title</label>
                <input id="issue" class="form-control h-100 rounded border-secondary-subtle" type="text" placeholder="Enter Ticket Title..."
                  v-model="form.issue" required/>
              </div>
            </div>
            <div class="d-flex flex-row gap-5 justify-content-center">
              <div class="d-flex flex-column flex-shrink-0 w-25">
                <label for="description" class="fw-semibold">Description</label>
                <textarea for="description" class="form-control auto-resize rounded border-secondary-subtle p-3" type="text"
                  rows="5" placeholder="Enter Ticket Description..." v-model="form.description" required> </textarea>
              </div>
            </div>
          </div>

          <!-- Button container -->
          <div class="container mt-3">
            <div class="row justify-content-end">
              <div class="col-md-6">
                <div class="d-flex gap-2">
                  <Button :name="'Submit'" :color="'primary'"></Button>
                  <Link :href="`/employee`" class="btn btn-outline-primary">Cancel</Link>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/EmployeeHeader.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";

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

<style scoped>
</style>
