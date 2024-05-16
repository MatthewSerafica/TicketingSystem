<template>
  <div>
    <Header></Header>
    <div class="mt-5 pt-5">
      <form @submit.prevent="changePassword">
        <div class="container">
          <div class="fw-bold mb-5 text-center">
            <h1 class="fw-bold">Change Password</h1>
            <div v-if="form.errors.oldPassword">{{ form.errors.oldPassword }}</div>
            <div class="text-danger">{{ page.props.flash.error }}</div>
            <div class="text-success">{{ page.props.flash.success }}</div>
          </div>

          <div class="d-flex flex-column justify-content-center align-items-center gap-2">
            <div class="form-group col-12 col-md-6">
              <label for="oldPassword" class="fw-semibold">Old Password</label>
              <input id="oldPassword" class="form-control rounded border-secondary-subtle" type="password"
                v-model="form.oldPassword" @input="checkOldPassword" />
            </div>
            <div class="form-group col-12 col-md-6 mt-3">
              <label for="newPassword" class="fw-semibold">New Password</label>
              <input id="newPassword" class="form-control rounded border-secondary-subtle" type="password"
                v-model="form.newPassword" />
              <div v-if="isNewPasswordTooShort" class="text-warning">New password should be at least 8 characters long</div>
            </div>
            <div class="form-group col-12 col-md-6 mt-3">
              <label for="confirmPassword" class="fw-semibold">Confirm Password</label>
              <input id="confirmPassword" class="form-control rounded border-secondary-subtle" type="password"
                v-model="form.confirmPassword" />
              <div v-if="!isFormValid && form.confirmPassword && form.newPassword" class="text-warning">Passwords do not match</div>
            </div>

            <!-- Right-aligned buttons -->
            <div class="col-12 col-md-6 mt-4 d-flex justify-content-end gap-2">
              <Button :name="'Submit'" :color="'primary'" :disabled="!isFormValid"></Button>
              <Link :href="`/technician`" class="btn btn-outline-secondary">Cancel</Link>
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
