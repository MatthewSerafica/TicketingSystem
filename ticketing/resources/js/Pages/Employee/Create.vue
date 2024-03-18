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
import Alpine from 'alpinejs';
import Toast from '@/Components/Toast.vue';
import { ref, watchEffect } from "vue";

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
