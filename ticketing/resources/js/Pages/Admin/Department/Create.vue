<template>
  <div>
  <Header></Header>
  <div class="mt-5 pt-5">
    <form @submit.prevent="create">
      <br />
      <div class="container">
        <div class="title-container fw-bold mb-5 text-center">
          <h1>Add Department</h1>
          <p class="fs-5"> Add Department Abbreviation</p>
        </div>

        <div class="create-department">
          <div class="d-flex flex-row gap-5 justify-content-center mb-4">
            <div class="d-flex flex-column flex-shrink-0 w-25">
              <label for="issue" class="fw-semibold">Department</label>
              <input id="issue" class="form-control h-100 rounded border-secondary-subtle" type="text" placeholder="Enter Department Abbreviation..." v-model="form.department" required/>
            </div>
            
          </div>

        </div>

        <div class="container">
          <div class="row justify-content-end">
            <div class="col-md-6">
              <div class="d-flex gap-2">
                <Button :name="'Submit'" :color="'primary'" class="submit-btn"></button>
                <Link :href="`/admin/department`" class=" btn btn-outline-primary">Cancel</Link>
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
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, useForm } from "@inertiajs/vue3";
import Button from '@/Components/Button.vue';

const props = defineProps({
  departments: Object,
})

const form = useForm({
  department: null,
})

const create = () => form.post(route('admin.department.store'), { preserveScroll: false, preserveState: false })
</script>
  
<style>

.submit-btn {
  transition: all 0.2s;
}

.submit-btn:hover {
  transform: scale(1.1);
}
</style>
  