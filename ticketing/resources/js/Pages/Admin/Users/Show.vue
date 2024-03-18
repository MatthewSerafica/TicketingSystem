<template>
    <div>
        <Header></Header>
        <div class="position-absolute end-0">
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
        <div class="w-25">
            <Link :href="route('admin.users')"
                class="btn btn-secondary m-2 d-flex flex-row justify-content-start align-items-center"
                style="width: 6rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                <path
                    d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z" />
            </svg>
            <span>Back</span>
            </Link>
        </div>
        <div class="d-flex flex-column gap-5 justify-content-center mt-3 align-items-center">
            <div v-if="user.employee" class="card shadow p-2" style="max-width: 50rem;">
                <div class="card-body d-flex flex-column gap-2">
                    <div class="card-title fw-bold fs-3">
                        User Details
                    </div>
                    <div class="d-flex flex-row gap-5">
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Name
                                </div>
                                <span v-if="!selectedInput || selectedInput !== user.name" class="card-text"
                                    @click="showInput(user.name)">
                                    {{ user.name }}
                                </span>
                                <input type="text" v-if="selectedInput === user.name" v-model="editData[user.name]"
                                    @blur="updateData(user.name, user.id, 'name', false, false)"
                                    @keyup.enter="updateData(user.name, user.id, 'name', false, false)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Email
                                </div>
                                <span v-if="!selectedInput || selectedInput !== user.email" class="card-text"
                                    @click="showInput(user.email)">{{ user.email }}</span>
                                <input type="text" v-if="selectedInput === user.email" v-model="editData[user.email]"
                                    @blur="updateData(user.email, user.id, 'email', false, false)"
                                    @keyup.enter="updateData(user.email, user.id, 'email', false, false)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    User Type
                                </div>
                                <p class="card-text text-capitalize">{{ user.user_type }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Department
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn text-start">
                                        {{ user.employee.department ? user.employee.department : 'Unassigned' }}
                                    </button>
                                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item disabled">Select a department</li>
                                        <li v-for="department in departments" class="btn dropdown-item"
                                            @click="showInput(department.department), updateData(department.department, user.employee.employee_id, 'department', true, false), console.log('Dropdown item clicked:', department.department)">
                                            {{ department.department }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Office
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn text-start text-break" style="max-width: 15rem;">
                                        {{ user.employee.office ? user.employee.office : 'Unassigned' }}
                                    </button>
                                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item disabled">Select an office</li>
                                        <li v-for="office in offices" class="btn dropdown-item"
                                            @click="showInput(office.office), updateData(office.office, user.employee.employee_id, 'office', true, false)">
                                            {{ office.office }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="user.technician" class="card shadow" style="width: 35rem;">
                <div class="card-body d-flex flex-column gap-1">
                    <div class="card-title fw-bold d-flex flex-row align-items-center gap-3">
                        <h3 class="mt-1">User Details</h3>
                        <span v-if="user.technician.is_working == 1" class="badge bg-success rounded-circle"
                            style="width: 2em; height: 2em;"><span class="visually-hidden">s</span></span>
                        <span v-if="user.technician.is_working == 0" class="badge bg-danger rounded-circle"
                            style="width: 2em; height: 2em;"><span class="visually-hidden">s</span></span>
                    </div>
                    <div class="d-flex flex-row gap-5">
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Name
                                </div>
                                <span v-if="!selectedInput || selectedInput !== user.name" class="card-text"
                                    @click="showInput(user.name, user.id)">
                                    {{ user.name }}
                                </span>
                                <input type="text" v-if="selectedInput === user.name" v-model="editData[user.name]"
                                    @blur="updateData(user.name, user.id, 'name', false, false)"
                                    @keyup.enter="updateData(user.name, user.id, 'name', false, false)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Email
                                </div>
                                <span v-if="!selectedInput || selectedInput !== user.email" class="card-text"
                                    @click="showInput(user.email, user.id)">
                                    {{ user.email }}
                                </span>
                                <input type="text" v-if="selectedInput === user.email" v-model="editData[user.email]"
                                    @blur="updateData(user.email, user.id, 'email', false, false)"
                                    @keyup.enter="updateData(user.email, user.id, 'email', false, false)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    User Type
                                </div>
                                <p class="card-text text-capitalize">{{ user.user_type }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Assigned Department
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn text-start">
                                        {{ user.technician.assigned_department ? user.technician.assigned_department :
                    'Unassigned' }}
                                    </button>
                                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item disabled">Select a department</li>
                                        <li v-for="department in departments" class="btn dropdown-item"
                                            @click="showInput(department.department), updateData(department.department, user.technician.technician_id, 'assigned_department', false, true), console.log('Dropdown item clicked:', department.department)">
                                            {{ department.department }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-6">
                                    Tickets Assigned
                                </div>
                                <p class="card-text">{{ user.technician.tickets_assigned }}</p>
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-6">
                                    Total Tickets Resolved
                                </div>
                                <p class="card-text">{{ user.technician.tickets_resolved }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="user.employee" class="d-flex flex-row align-items-center justify-content-center"
                style="gap:10rem;">
                <div class="d-flex flex-row card p-5 gap-5 shadow">
                    <div class="">
                        <Doughnut style="width: 20rem;"></Doughnut>
                    </div>
                    <div class="=">
                        <Bar style="width: 40rem;"></Bar>
                    </div>
                </div>
            </div>
            <div v-if="user.technician" class="d-flex flex-row align-items-center justify-content-center"
                style="gap:10rem;">
                <div class="d-flex flex-row card p-5 gap-5 shadow">
                    <div class="">
                        <Doughnut style="width: 20rem;"></Doughnut>
                    </div>
                    <div class="">
                        <Bar style="width: 40rem;"></Bar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { reactive, ref, watchEffect } from 'vue';
import Bar from '@/Pages/Admin/Users/Charts/Bar.vue';
import Doughnut from '@/Pages/Admin/Users/Charts/Doughnut.vue';
import Alpine from 'alpinejs';
import Toast from '@/Components/Toast.vue';

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

const props = defineProps({
    user: Object,
    offices: Object,
    departments: Object,
})

let selectedInput = ref(null);
let editData = reactive({});

const showInput = (data) => {
    selectedInput.value = data;
    editData[data] = data ? data : '';
}

const updateData = async (data, id, updateField, isEmployee, isTechnician) => {
    if (selectedInput.value === data) {
        const routeName = isEmployee ? 'admin.users.update.employee' : isTechnician ? 'admin.users.update.technician' : 'admin.users.update';
        const form = useForm({
            [updateField]: editData[data],
        });
        console.log(routeName)

        await form.put(route(routeName, { user_id: id, field: updateField }));

        selectedInput.value = null;
        editData[data] = '';
    }
};

</script>