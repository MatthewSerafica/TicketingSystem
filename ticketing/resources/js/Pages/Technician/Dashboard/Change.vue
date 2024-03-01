<template>
  <div>
    <Header></Header>
    <div class="mt-5 pt-5">
      <form @submit.prevent="changePassword">
        <br />
        <div class="container">
          <div class="title-container fw-bold mb-5 text-center">
            <h1>Change Password</h1>
            <div v-if="form.errors.oldPassword">{{ form.errors.oldPassword }}</div>
            <div class="text-danger">{{ page.props.flash.error }}</div>
            <div class="text-success">{{ page.props.flash.success }}</div>
          </div>

          <div class="d-flex flex-column justify-content-center align-items-center gap-2">
            <div class="d-flex flex-column flex-shrink-0 w-25">
              <label for="oldPassword" class="fw-semibold">Old Password</label>
              <input id="oldPassword" class="form-control h-100 rounded border-secondary-subtle" type="password"
                v-model="form.oldPassword" @input="checkOldPassword" />
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25 mt-3">
              <label for="newPassword" class="fw-semibold">New Password</label>
              <input id="newPassword" class="form-control h-100 rounded border-secondary-subtle" type="password"
                v-model="form.newPassword" />
              <div v-if="isNewPasswordTooShort" class="text-warning">New password should be at least 8 characters long</div>
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25 mt-3">
              <label for="confirmPassword" class="fw-semibold">Confirm Password</label>
              <input id="confirmPassword" class="form-control h-100 rounded border-secondary-subtle" type="password"
                v-model="form.confirmPassword" />
            </div>
            <div v-if="!isFormValid" class="text-warning">Passwords do not match</div>
          </div>

          <div class="container">
            <div class="row justify-content-center">
              <div class="col-md-6">
                <div class="d-flex justify-content-center gap-2 mt-4">
                  <Button :name="'Submit'" :color="'primary'" :disabled="!isFormValid"></Button>
                  <Link :href="`/technician`" class=" btn btn-outline-primary">Cancel</Link>
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
import Header from "@/Pages/Layouts/TechnicianHeader.vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import Button from '@/Components/Button.vue';
import { computed, ref } from 'vue';

const page = usePage()

const props = defineProps({
  user: Object,
});

const form = useForm({
  newPassword: null,
  confirmPassword: null,
  oldPassword: ref(''),
});

const isNewPasswordTooShort = computed(() => {
  return form.newPassword && form.newPassword.length < 8;
});

const isFormValid = computed(() => {
  const { oldPassword, newPassword, confirmPassword } = form;
  const allFieldsFilled = oldPassword && newPassword && confirmPassword;
  const passwordsMatch = newPassword === confirmPassword;
  return allFieldsFilled && passwordsMatch;
});

const changePassword = async () => {
  try {
    await form.post(route('technician.change-password', { user_id: page.props.user.id }));
    if (page.props.flash.success) {
      // Access page.props.flash.success here
      console.log(page.props.flash.success);
    }
  } catch (error) {
    console.error('Error changing password:', error);
  }
};
</script>
  
<style scoped>
.disabled {
  pointer-events: none;
  opacity: 0.5;
}
</style>
