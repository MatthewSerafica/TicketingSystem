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
            <div class="d-flex flex-row gap-5 justify-content-center mb-4">
              <div class="d-flex flex-column flex-shrink-0 w-25">
                <label for="issue" class="fw-semibold">Title</label>
                <input id="issue" class="form-control h-100 rounded border-secondary-subtle" type="text" placeholder="Enter Ticket Title..."
                  v-model="form.issue" />
              </div>

            </div>
            <div class="d-flex flex-row gap-5 justify-content-center">
              <div class="d-flex flex-column flex-shrink-0 w-25">
                <label for="description" class="fw-semibold">Description</label>
                <textarea for="description" class="form-control auto-resize rounded border-secondary-subtle p-3" type="text"
                  placeholder="Enter Ticket Description..." v-model="form.description"> </textarea>
              </div>
            </div>
          </div>


          <div class="container">
            <div class="row justify-content-center">
              <div class="col-md-6">
              <div class="d-flex justify-content-center gap-2">
                <Link :href="`/employee`" class="btn btn-outline-primary">Cancel</Link>
                <button class="submit-ticket-button btn btn-primary" type="submit" as="button">Submit</button>
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
  
  