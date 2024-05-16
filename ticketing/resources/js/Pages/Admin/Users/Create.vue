<template>
    <div>
        <Header></Header>
            <div class="mt-2">
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
                        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown"
                        v-if="showErrorToast" :error="page.props.flash.error" :error_message="page.props.flash.error_message"
                        @close="handleClose">
                    </Toast>
                </div>
        <form @submit.prevent="create">
            <br />
            <div class="container">
                <div class="title-container fw-bold text-center">
                    <h1 class="fw-bold">Create Users</h1>
                </div>

                
                    <div class="d-flex flex-column">
                        <label for="user_type" class="fw-semibold">User Type</label>
                        <select id="user_type" class="form-select h-100 rounded border-secondary-subtle"
                            placeholder="Select User Type..." v-model="form.user_type">
                            <option disabled>Select User Type</option>
                            <option value="employee">Employee</option>
                            <option value="technician">Technician</option>
                            <option value="observer">Observer</option>
                        </select>
                    </div>

                    <div class="d-flex flex-row gap-3 justify-content-center">
                        <div class="col-md-9">
                            <div class="d-flex flex-column">
                                <label for="name" class="fw-semibold">Name</label>
                                <input id="name" class="form-control rounded border-secondary-subtle" type="text"
                                    placeholder="First and Last Name..." v-model="form.name" required />

                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex flex-column">
                                <label for="email" class="fw-semibold">Email</label>
                                <input id="email" class="form-control h-100 rounded border-secondary-subtle"
                                    type="email" placeholder="Enter Email..." v-model="form.email" required />
                                <div v-if="form.errors.email && form.errors.email.length > 0" class="text-danger">
                                    {{ form.errors.email[0] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-3 justify-content-center">
                        <div class="col-md-9">
                            <div class="d-flex flex-column">
                                <label for="password" class="fw-semibold">Password</label>
                                <input id="password" class="form-control rounded border-secondary-subtle"
                                    type="password" placeholder="Enter Password..." v-model="form.password" required />
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="d-flex flex-column">
                                <label for="conf" class="fw-semibold">Confirm Password</label>
                                <input id="conf" class="form-control rounded border-secondary-subtle" type="password"
                                    placeholder="Confirm Password..." v-model="form.conf" required />
                            </div>
                        </div>
                    </div>

                    <div v-if="form.errors.conf && form.errors.conf.length > 0" class="text-danger p-0">
                        {{ form.errors.conf[0] }}
                    </div>
                    <div v-if="form.errors.password && form.errors.password.length > 0" class="text-danger">
                        {{ form.errors.password[0] }}
                    </div>
                    <div v-if="form.user_type === 'employee'" class="d-flex flex-row gap-3 justify-content-center">
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <label for="department" class="fw-semibold">Department</label>
                                <select id="department" class="form-select h-100 rounded border-secondary-subtle"
                                    placeholder="Select Department..." v-model="form.department">
                                    <option disabled>Select Department</option>
                                    <option v-for="department in departments" :value="department.department">
                                        {{ department.department }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <label for="office" class="fw-semibold">Office</label>
                                <select id="office" class="form-select h-100 rounded border-secondary-subtle"
                                    placeholder="Select Department..." v-model="form.office">
                                    <option disabled>Select Office</option>
                                    <option v-for="office in offices" :value="office.office">
                                        {{ office.office }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.user_type === 'technician'"
                        class="d-flex flex-row gap-3 justify-content-center w-25">
                        <div class="col-md-10">
                            <div class="d-flex flex-column">
                                <label for="assigned" class="fw-semibold">Assign a Department</label>
                                <select id="assigned" class="form-select h-100 rounded border-secondary-subtle"
                                    placeholder="Select Department..." v-model="form.assigned">
                                    <option disabled>Select Department</option>
                                    <option v-for="department in departments" :value="department.department">
                                        {{ department.department }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            

                <div class="container mt-3">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end gap-2">
                                <Button :name="'Submit'" :color="'primary'" class="submit-btn"></button>
                                <Link :href="`/admin/users`" class=" btn btn-outline-secondary">Cancel</Link>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import { defineProps, ref, watchEffect } from 'vue';

// Toast Start
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
// Toast End

const props = defineProps({
    departments: Object,
    offices: Object,
})

const form = useForm({
    user_type: null,
    name: null,
    email: null,
    password: null,
    conf: null,
    department: null,
    office: null,
    assigned: null,
})

const create = async () => {
    if (form.password !== form.conf) {
        form.errors.conf = ["Passwords don't match!"];
        return;
    }

    if (form.password.length < 8) {
        form.errors.password = ["Password must be at least 8 characters long"];
        return;

    }

    if (!form.email.endsWith('slu.edu.ph')) {
        form.errors.email = ['Email must end with slu.edu.ph'];
        return;
    }

    try {
        await form.post(route('admin.users.store'), { preserveScroll: false, preserveState: false });
    } catch (error) {
        console.error('Error submitting form:', error);
    }
};

</script>