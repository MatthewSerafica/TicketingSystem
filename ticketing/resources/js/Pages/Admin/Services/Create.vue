<template>
    <div>
    <Header></Header>
      <div class="d-flex flex-column justify-content-center align-items-center">
        <div class="mt-5 pt-5">
          <form @submit.prevent="create" class="form-container">
            <div class="title-container fw-bold mb-5 text-center">
              <h1 class="fw-bold">Add Service</h1>
            </div>
            <div class="mb-3">
              <label for="issue" class="fw-semibold">Service</label>
              <input id="issue" class="form-control h-100 rounded border-secondary-subtle" type="text" placeholder="Enter Service..." v-model="form.service" required/>
            </div>
            <div class="d-flex justify-content-end gap-2">
              <Button :name="'Add'" :color="'primary'" class="submit-btn"></button>
              <Link :href="`/admin/services`" class=" btn btn-outline-secondary">Cancel</Link>
            </div>
          </form>
        </div>
      </div>  
    </div>
  </template>
    
  <script setup>
  import Header from "@/Pages/Layouts/AdminHeader.vue";
  import { Link, useForm } from "@inertiajs/vue3";
  import Button from '@/Components/Button.vue';
  
  const props = defineProps({
    service: Object,
  })
  
  const form = useForm({
    service: null,
  })
  
  const create = () => form.post(route('admin.services.store'), { preserveScroll: false, preserveState: false })
  </script>
    
  <style>
  
  .submit-btn {
    transition: all 0.2s;
  }
  
  .submit-btn:hover {
    transform: scale(1.1);
  }
  </style>
    