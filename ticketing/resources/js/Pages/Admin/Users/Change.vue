<template>
  <div>
    <Header></Header>
    {{ user }}
    {{ page.props.flash.error }}
    {{ page.props.flash.success }}
    <div class="mt-5 pt-5">
      <form @submit.prevent="changePassword">
        <br />
        <div class="container">
          <div class="title-container fw-bold mb-5 text-center">
            <h1>Change Password</h1>
            <div v-if="form.errors.oldPassword">{{ form.errors.oldPassword }}</div>
          </div>

          <div class="d-flex flex-column justify-content-center align-items-center gap-2">
            <div class="d-flex flex-column flex-shrink-0 w-25">
              <label for="oldPassword" class="fw-semibold">Old Password</label>
              <input id="oldPassword" class="form-control h-100 rounded border-secondary-subtle" type="password"
                v-model="form.oldPassword" @input="checkOldPassword" />
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25 mt-3" :class="{ 'disabled': !isOldPasswordEntered }">
              <label for="newPassword" class="fw-semibold">New Password</label>
              <input id="newPassword" class="form-control h-100 rounded border-secondary-subtle" type="password"
                v-model="form.newPassword" :disabled="!isOldPasswordEntered" />
            </div>
            <div class="d-flex flex-column flex-shrink-0 w-25 mt-3" :class="{ 'disabled': !isOldPasswordEntered }">
              <label for="confirmPassword" class="fw-semibold">Confirm Password</label>
              <input id="confirmPassword" class="form-control h-100 rounded border-secondary-subtle" type="password"
                v-model="form.confirmPassword" :disabled="!isOldPasswordEntered" />
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
import { Link, usePage, router, useForm } from "@inertiajs/vue3";
import Button from '@/Components/Button.vue';
import { reactive, computed, ref } from 'vue';

const page = usePage()

const props = defineProps({
  user: Object,
});

const form = useForm({
  newPassword: null,
  confirmPassword: null,
  oldPassword: ref(''),
});

const oldPassword = ref('');
const isOldPasswordEntered = computed(() => form.oldPassword !== '');


const changePassword = async () => {

  await form.post(route('admin.change-password', { user_id: page.props.user.id }));
};


</script>
  
<style scoped>
.disabled {
  pointer-events: none;
  opacity: 0.5;
}
</style>
  