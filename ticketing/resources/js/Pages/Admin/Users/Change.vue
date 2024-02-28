<template>
    <div>
      <Header></Header>
      <div class="mt-5 pt-5">
        <form @submit.prevent="changePassword">
          <br />
          <div class="container">
            <div class="title-container fw-bold mb-5 text-center">
              <h1>Change Password</h1>
            </div>
  
            <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                <div class="d-flex flex-column flex-shrink-0 w-25">
                <label for="oldPassword" class="fw-semibold">Old Password</label>
                <input id="oldPassword" class="form-control h-100 rounded border-secondary-subtle" type="password" v-model="oldPassword" @input="checkOldPassword" />
                </div>
                <div class="d-flex flex-column flex-shrink-0 w-25 mt-3" :class="{ 'disabled': !isOldPasswordEntered }">
                <label for="newPassword" class="fw-semibold">New Password</label>
                <input id="newPassword" class="form-control h-100 rounded border-secondary-subtle" type="password" v-model="form.newPassword" :disabled="!isOldPasswordEntered" />
                </div>
                <div class="d-flex flex-column flex-shrink-0 w-25 mt-3" :class="{ 'disabled': !isOldPasswordEntered }">
                <label for="confirmPassword" class="fw-semibold">Confirm Password</label>
                <input id="confirmPassword" class="form-control h-100 rounded border-secondary-subtle" type="password" v-model="form.confirmPassword" :disabled="!isOldPasswordEntered" />
                </div>
            </div>
  
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-md-6">
                  <div class="d-flex justify-content-center gap-2 mt-4">
                    <Button :name="'Submit'" :color="'primary'"></Button>
                    <Link :href="`/admin/users`" class=" btn btn-outline-primary">Cancel</Link>
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
  import { Link, usePage, router } from "@inertiajs/vue3";
  import Button from '@/Components/Button.vue';
  import { reactive, computed, ref } from 'vue';
  
  const props = defineProps({
  user: Object,
});

  const form = reactive({
    newPassword: '',
    confirmPassword: ''
  });
  
  const oldPassword = ref('');
  const isOldPasswordEntered = computed(() => oldPassword.value !== '');
  
  const { $inertia } = usePage();
  
  const changePassword = async () => {
    const formData = {
      oldPassword: oldPassword.value,
      newPassword: form.newPassword,
      confirmPassword: form.confirmPassword
    };
    
    const { data, status } = await $inertia.post(router('admin.change-password', { user_id: $page.props.user.id }), formData);
    
    if (status === 'success') {
      console.log('Password changed successfully');
    } else {
      console.error('Failed to change password');
    }
  };
  
  const checkOldPassword = () => {
  const storedPassword = props.user.password;
  if (oldPassword.value === storedPassword) {
    console.log('Old password is correct');
    form.newPassword = ''; // Reset new password field
    form.confirmPassword = ''; // Reset confirm password field
  } else {
    // Old password doesn't match stored password
    console.error('Old password is incorrect');
    // You can disable the new password and confirm password fields here
    form.newPassword = ''; // Reset new password field
    form.confirmPassword = ''; // Reset confirm password field
  }
};

  </script>
  
  <style scoped>
  .disabled {
    pointer-events: none;
    opacity: 0.5;
  }
  </style>
  