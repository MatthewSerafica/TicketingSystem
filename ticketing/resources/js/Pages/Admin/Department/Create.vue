<template>
  <div>
    <Header></Header>
    <div class="d-flex flex-column justify-content-center align-items-center">
      <div class="position-absolute end-0">
        <!-- Toast components here -->
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
        <form @submit.prevent="create" class="form-container">
          <div class="title-container fw-bold mb-5 text-center">
            <h1 class="fw-bold">Add Department</h1>
            <p class="fs-5">Add Department Abbreviation</p>
          </div>
          <div class="mb-3">
            <label for="issue" class="fw-semibold">Department</label>
            <input id="issue" class="form-control rounded border-secondary-subtle" type="text" placeholder="Enter Department Abbreviation..." v-model="form.department" required />
          </div>
          <div class="d-flex justify-content-end gap-2">
            <Button :name="'Add'" :color="'primary'" class="submit-btn"></Button>
            <Link :href="`/admin/department`" class="btn btn-outline-secondary">Cancel</Link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>


<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Button from '@/Components/Button.vue';
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