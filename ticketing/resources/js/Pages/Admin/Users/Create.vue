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
                    v-if="showErrorToast" :error="page.props.flash.error"
                    :error_message="page.props.flash.error_message" @close="handleClose">
                </Toast>
            </div>
            <form @submit.prevent="create">
                <br />
                <div class="container">
                    <div class="title-container fw-bold text-center">
                        <h1 class="fw-bold">Create Users</h1>
                    </div>
                    <div class="row mb-3 justify-content-center">
                        <div class="col-md-6">
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
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <label for="name" class="fw-semibold">Name</label>
                                <input id="name" class="form-control rounded border-secondary-subtle" type="text"
                                    placeholder="First and Last Name..." v-model="form.name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
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

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex flex-column">
                                <label for="password" class="fw-semibold">Password</label>
                                <input id="password" class="form-control rounded border-secondary-subtle"
                                    type="password" placeholder="Enter Password..." v-model="form.password" required />
                            </div>
                        </div>
                        <div class="col-md-6">
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

                    <div v-if="form.user_type === 'employee'" class="row mb-3">
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
                        class="row mb-3 justify-content-center align-items-center">
                        <div class="col-md-6 justify-content-center align-items-center d-flex flex-column gap-3">
                            <label for="assigned" class="fw-semibold">Assign a Department</label>
                            <div class="d-flex flex-column justify-content-center align-items-center gap-2 w-100">
                                <div v-for="(dropdown, index) in departmentData" :key="index">
                                    <div class="flex-grow-1 w-100 d-flex flex-row gap-2">
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-outline-secondary text-start text-secondary-emphasis w-100"
                                                aria-expanded="false">
                                                {{ dropdown.department ? dropdown.department
                        : 'Assign a department...' }}
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-reference="parent">
                                                <span class="visually-hidden">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                                                <div>
                                                    <h6 class="dropdown-header">All</h6>
                                                    <li v-for="department in departments" class="btn dropdown-item"
                                                        @click="selectDepartment(department, index)">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <span class="fw-semibold">
                                                                    {{ department.department }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li v-if="!departments || departments.length === 0">
                                                        No departments available</li>
                                                </div>
                                            </ul>
                                        </div>
                                        <button type="button"
                                            class="btn border-0 rounded-pill d-flex justify-content-center align-items-center fs-5 text-danger"
                                            @click="removeDropdown(index)">
                                            <i class="bi bi-dash-circle-fill"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="show">
                                    <button type="button" as="button"
                                        class="btn align-items-center justify-content-center d-flex text-primary fs-5 gap-2"
                                        style="height:1.5em;" @click="addDropdown">
                                        <i class="bi bi-plus-circle-fill"></i>
                                        <span v-if="showLabel">Assign Department</span>
                                    </button>
                                </div>
                                <span v-if="form.errors.departments" class="error-message">
                                {{ form.errors.departments }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container mt-3">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end gap-2">
                                <Button :name="'Submit'" :color="'primary'" class="submit-btn"></button>
                                <Link :href="`/admin/users`" class="btn btn-outline-secondary">Cancel</Link>
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

let show = ref(true);

let departmentData = ref([]);

const addDropdown = () => {
    departmentData.value.push({
        department_id: null,
        department: ref(''),
    })
}

const removeDropdown = (index) => {
    const removedDepartmentId = departmentData.value[index].department_id;
    departmentData.value.splice(index, 1);

    const departmentIndex = form.departments.indexOf(removedDepartmentId);
    if (departmentIndex !== -1) {
        form.techdepartmentsnicians.splice(departmentIndex, 1);
    }
}

const selectDepartment = (department, index) => {
    if (form.departments.includes(department.id)) {
        form.errors.departments = 'Technician is already selected.';
        return;
    }

    if (departmentData.value.some(item => item.department_id === department.id)) {
        form.errors.technician = 'Technician is already selected.';
        return;
    }

    departmentData.value[index].department = department.department;
    departmentData.value[index].department_id = department.id;

    form.departments.push(department.id);

    form.errors.departments = null;
};

const form = useForm({
    user_type: null,
    name: null,
    email: null,
    password: null,
    conf: null,
    departments: [],
    department: null,
    office: null,
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
