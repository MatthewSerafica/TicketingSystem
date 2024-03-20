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
            <Link :href="route('employee')"
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
            <div v-if="users.employee" class="card shadow p-2" style="max-width: 50rem;">
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
                                <span v-if="!selectedInput || selectedInput !== users.name" class="card-text"
                                    @click="showInput(users.name)">
                                    {{ users.name }}
                                </span>
                                <input type="text" v-if="selectedInput === users.name" v-model="editData[users.name]"
                                    @blur="updateData(users.name, user.id, 'name', false, false)"
                                    @keyup.enter="updateData(users.name, users.id, 'name', false, false)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Email
                                </div>
                                <span v-if="!selectedInput || selectedInput !== users.email" class="card-text"
                                    @click="showInput(users.email)">{{ users.email }}</span>
                                <input type="text" v-if="selectedInput === users.email" v-model="editData[users.email]"
                                    @blur="updateData(users.email, users.id, 'email', false, false)"
                                    @keyup.enter="updateData(users.email, users.id, 'email', false, false)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    User Type
                                </div>
                                <p class="card-text text-capitalize">{{ users.user_type }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Department
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn text-start">
                                        {{ users.employee.department ? users.employee.department : 'Unassigned' }}
                                    </button>
                                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item disabled">Select a department</li>
                                        <li v-for="department in departments" class="btn dropdown-item"
                                            @click="showInput(department.department), updateData(department.department, users.employee.employee_id, 'department', true, false), console.log('Dropdown item clicked:', department.department)">
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
                                        {{ users.employee.office ? users.employee.office : 'Unassigned' }}
                                    </button>
                                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item disabled">Select an office</li>
                                        <li v-for="office in offices" class="btn dropdown-item"
                                            @click="showInput(office.office), updateData(office.office, users.employee.employee_id, 'office', true, false)">
                                            {{ office.office }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-row align-items-center justify-content-center"
                style="gap:10rem;">
                <div class="d-flex flex-row card p-5 gap-5 shadow">
                    <div class="">
                        <Doughnut :service="service" style="width: 20rem;"></Doughnut>
                    </div>
                    <div class="=">
                        <Bar :yearly="yearly" style="width: 40rem;"></Bar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Toast from '@/Components/Toast.vue';
import Header from '@/Pages/Layouts/EmployeeHeader.vue';
import Bar from '@/Pages/Employee/Charts/Bar.vue';
import Doughnut from '@/Pages/Employee/Charts/Doughnut.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Alpine from 'alpinejs';
import { reactive, ref, watchEffect } from 'vue';

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
    users: Object,
    departments: Object,
    yearly: Object,
    service: Object,
})

let selectedInput = ref(null);
let editData = reactive({});

const showInput = (data) => {
    selectedInput.value = data;
    editData[data] = data ? data : '';
}

/* const updateData = async (data, id, updateField, isEmployee, isTechnician) => {
    if (selectedInput.value === data) {
        const routeName = 'technician.users.update.technician';
        const form = useForm({
            [updateField]: editData[data],
        });
        console.log(routeName)

        await form.put(route(routeName, { user_id: id, field: updateField }));

        selectedInput.value = null;
        editData[data] = '';
    }
}; */

</script>