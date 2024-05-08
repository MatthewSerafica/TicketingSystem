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
            <a :href="route('admin.users')"
                class="btn btn-secondary m-2 d-flex flex-row justify-content-start align-items-center back-button"
                style="width: 6rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                    <path
                        d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z" />
                </svg>
                <span>Back</span>
            </a>
        </div>
        <div class="d-flex flex-column gap-4 justify-content-center align-items-center mt-3 mb-3 main-content">
            <div v-if="users.user_type === 'employee'" class="card shadow p-2">
                <div class="card-body d-flex flex-column gap-4">
                    <div class="card-title fw-bold fs-3">
                        User Details
                    </div>
                    <div class="d-flex flex-row gap-3">
                        <div>
                            <div class="card-subtitle fw-medium fs-5">
                                Name
                            </div>
                            <span v-if="!selectedInput || selectedInput !== users.name" class="card-text"
                                @click="showInput(users.name)">
                                {{ users.name }}
                            </span>
                            <input type="text" v-if="selectedInput === users.name" v-model="editData[users.name]"
                                @blur="updateData(users.name, users.id, 'name', false, false)"
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
                    <div class="d-flex flex-row gap-3">
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
            <div v-if="users.user_type === 'technician'"
                class="d-flex gap-5 justify-content-between align-items-center detail-container">
                <div class="card shadow p-2 user-container"
                    style="border-top: 1px; border-left: 1px; border-right: 1px; border-bottom: 1px;">
                    <div class="card-body d-flex flex-column gap-4 user-card-container">
                        <div>
                            <div class="card-title fw-bold d-flex flex-row align-items-center gap-3">
                                <h3 class="mt-1">User Details</h3>
                                <span v-if="users.technician.is_working == 1" class="badge bg-success rounded-circle"
                                    style="width: 2em; height: 2em;"><span class="visually-hidden">s</span></span>
                                <span v-if="users.technician.is_working == 0" class="badge bg-danger rounded-circle"
                                    style="width: 2em; height: 2em;"><span class="visually-hidden">s</span></span>
                            </div>
                            <div class="d-flex flex-row gap-5">
                                <div>
                                    <div class="card-subtitle fw-medium fs-5">
                                        Name
                                    </div>
                                    <span v-if="!selectedInput || selectedInput !== users.name" class="card-text"
                                        @click="showInput(users.name, users.id)">
                                        {{ users.name }}
                                    </span>
                                    <input type="text" v-if="selectedInput === users.name"
                                        v-model="editData[users.name]"
                                        @blur="updateData(users.name, users.id, 'name', false, false)"
                                        @keyup.enter="updateData(users.name, users.id, 'name', false, false)"
                                        class="rounded border border-secondary-subtle text-start">
                                </div>
                                <div>
                                    <div class="card-subtitle fw-medium fs-5">
                                        Email
                                    </div>
                                    <span v-if="!selectedInput || selectedInput !== users.email" class="card-text"
                                        @click="showInput(users.email, users.id)">
                                        {{ users.email }}
                                    </span>
                                    <input type="text" v-if="selectedInput === users.email"
                                        v-model="editData[users.email]"
                                        @blur="updateData(users.email, users.id, 'email', false, false)"
                                        @keyup.enter="updateData(users.email, users.id, 'email', false, false)"
                                        class="rounded border border-secondary-subtle text-start">
                                </div>
                                <div>
                                    <div class="card-subtitle fw-medium fs-5">
                                        User Type
                                    </div>
                                    <div>
                                        <button type="button" class="btn text-start text-capitalize">
                                            {{ users.user_type ? users.user_type : 'Unassigned' }}
                                        </button>
                                        <button v-if="users.user_type !== 'super' && page.props.user.user_type !== 'admin'" type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul v-if="users.user_type !== 'super' && page.props.user.user_type !== 'admin'" class="dropdown-menu">
                                            <li class="dropdown-item disabled">Select a user type</li>
                                            <li class="btn dropdown-item"
                                                @click="showInput('technician'), updateData('technician', users.id, 'user_type', false, false)">
                                                Technician
                                            </li>
                                            <li class="btn dropdown-item"
                                                @click="showInput('admin'), updateData('admin', users.id, 'user_type', false, false)">
                                                Admin
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Assigned Department
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn text-start">
                                        {{ users.technician.assigned_department ?
                    users.technician.assigned_department :
                    'Unassigned' }}
                                    </button>
                                    <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item disabled">Select a department</li>
                                        <li v-for="department in departments" class="btn dropdown-item"
                                            @click="showInput(department.department), updateData(department.department, users.technician.technician_id, 'assigned_department', false, true), console.log('Dropdown item clicked:', department.department)">
                                            {{ department.department }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gap-3 data-container">
                    <div class="d-flex gap-3 data-top">
                        <div class="assigned-total card border-3 border-primary p-2 shadow"
                            style="border-top: 1px; border-left: 1px; border-right: 1px;">
                            <div class="card-body d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-3">
                                    <div class="fs-5 text-secondary">
                                        Assigned (Total)
                                    </div>
                                    <div class="fw-semibold fs-2">
                                        {{ users.technician.tickets_assigned }} <span
                                            class="fw-normal text-secondary fs-6">assigned</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="resolved-total card border-3 border-success p-2 shadow"
                            style="border-top: 1px; border-left: 1px; border-right: 1px;">
                            <div class="card-body d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-3">
                                    <div class="fs-5 text-secondary">
                                        Resolved (Total)
                                    </div>
                                    <div class="fw-semibold fs-2">
                                        {{ users.technician.tickets_resolved }} <span
                                            class="fw-normal text-secondary fs-6">resolved</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="resolved-total card border-3 border-info p-2 shadow"
                            style="border-top: 1px; border-left: 1px; border-right: 1px;">
                            <div class="card-body d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-1">
                                    <div class="fs-5 text-secondary">
                                        Complexity (Total)
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="fw-semibold fs-4">
                                            {{ complexity.simple }}
                                            <span class="fw-normal text-secondary fs-6">Simple</span>
                                        </div>
                                        <div class="fw-semibold fs-4">
                                            {{ complexity.complex }}
                                            <span class="fw-normal text-secondary fs-6">Complex</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 data-bottom">
                        <div class="assigned-today card border-3 border-danger p-2 shadow"
                            style="border-top: 1px; border-left: 1px; border-right: 1px;">
                            <div class="card-body d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-3">
                                    <div class="fs-5 text-secondary">
                                        Assigned (Today)
                                    </div>
                                    <div class="fw-semibold fs-2 ">
                                        {{ assigned_today }}
                                        <span class="fw-normal text-secondary fs-6">assigned</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="resolved-today card border-3 border-success p-2 shadow"
                            style="border-top: 1px; border-left: 1px; border-right: 1px;">
                            <div class="card-body d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-3">
                                    <div class="fs-5 text-secondary">
                                        Resolved (Today)
                                    </div>
                                    <div class="fw-semibold fs-2">
                                        {{ resolved_today }}
                                        <span class="fw-normal text-secondary fs-6">resolved</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="resolved-today card border-3 border-warning p-2 shadow"
                            style="border-top: 1px; border-left: 1px; border-right: 1px;">
                            <div class="card-body d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-1">
                                    <div class="fs-5 text-secondary">
                                        Average Resolution Time
                                    </div>
                                    <div class="fw-semibold fs-3">
                                        {{ time }}
                                        <span class="fw-normal text-secondary fs-6">Hour(s)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="users.user_type === 'admin' || users.user_type === 'super'"
                class="d-flex gap-5 justify-content-between align-items-center detail-container">
                <div class="card shadow p-2 user-container"
                    style="border-top: 1px; border-left: 1px; border-right: 1px; border-bottom: 1px;">
                    <div class="card-body d-flex flex-column gap-4 user-card-container">
                        <div>
                            <div class="card-title fw-bold d-flex flex-row align-items-center gap-3">
                                <h3 class="mt-1">User Details</h3>
                            </div>
                            <div class="d-flex flex-row gap-5">
                                <div>
                                    <div class="card-subtitle fw-medium fs-5">
                                        Name
                                    </div>
                                    <span v-if="!selectedInput || selectedInput !== users.name" class="card-text"
                                        @click="showInput(users.name, users.id)">
                                        {{ users.name }}
                                    </span>
                                    <input type="text" v-if="selectedInput === users.name"
                                        v-model="editData[users.name]"
                                        @blur="updateData(users.name, users.id, 'name', false, false)"
                                        @keyup.enter="updateData(users.name, users.id, 'name', false, false)"
                                        class="rounded border border-secondary-subtle text-start">
                                </div>
                                <div>
                                    <div class="card-subtitle fw-medium fs-5">
                                        Email
                                    </div>
                                    <span v-if="!selectedInput || selectedInput !== users.email" class="card-text"
                                        @click="showInput(users.email, users.id)">
                                        {{ users.email }}
                                    </span>
                                    <input type="text" v-if="selectedInput === users.email"
                                        v-model="editData[users.email]"
                                        @blur="updateData(users.email, users.id, 'email', false, false)"
                                        @keyup.enter="updateData(users.email, users.id, 'email', false, false)"
                                        class="rounded border border-secondary-subtle text-start">
                                </div>
                                <div>
                                    <div class="card-subtitle fw-medium fs-5">
                                        User Type
                                    </div>
                                    <div>
                                        <button type="button" class="btn text-start text-capitalize">
                                            {{ users.user_type ? users.user_type : 'Unassigned' }}
                                        </button>
                                        <button v-if="users.user_type !== 'super' && page.props.user.user_type !== 'admin'" type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul v-if="users.user_type !== 'super' && page.props.user.user_type !== 'admin'" class="dropdown-menu">
                                            <li class="dropdown-item disabled">Select a user type</li>
                                            <li class="btn dropdown-item"
                                                @click="showInput('technician'), updateData('technician', users.id, 'user_type', false, false)">
                                                Technician
                                            </li>
                                            <li class="btn dropdown-item"
                                                @click="showInput('admin'), updateData('admin', users.id, 'user_type', false, false)">
                                                Admin
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="align-items-center justify-content-center gap-4 statistics">
                <div class="card text-left border-0 shadow">
                    <h5 class="card-header text-secondary">
                        Tickets by Services
                    </h5>
                    <div class="card-body">
                        <Doughnut :service="service" class="doughnut"></Doughnut>
                    </div>
                </div>
                <div class="card text-left border-0 shadow">
                    <h5 class="card-header text-secondary">
                        Tickets for this Year
                    </h5>
                    <div class="card-body">
                        <Bar :yearly="yearly" class="bar"></Bar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Toast from '@/Components/Toast.vue';
import Bar from '@/Pages/Admin/Users/Charts/Bar.vue';
import Doughnut from '@/Pages/Admin/Users/Charts/Doughnut.vue';
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { useForm, usePage } from '@inertiajs/vue3';
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
    offices: Object,
    departments: Object,
    yearly: Object,
    service: Object,
    assigned_today: Object,
    resolved_today: Object,
    time: Object,
    complexity: Object,
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

<style scoped>
.btn:hover {
    scale: 0.97;
}

.dropdown-menu {
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.dropdown-menu.show {
    display: block;
    opacity: 1;
}

.dropdown-item {
    opacity: 0;
    transition: opacity 0.5s ease;
}

.dropdown-menu.show .dropdown-item {
    opacity: 1;
}

.dropdown-item {
    animation: fadeIn 0.5s ease forwards;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
    }

    100% {
        opacity: 1;
    }
}

.back-button {
    width: 6rem;
    transition: transform 0.3s ease;
}

.back-button:hover {
    transform: scale(1.1);
}

.doughnut {
    width: 37rem;
}

.bar {
    width: 50rem;
}

.assigned-total {
    width: 13rem;
}

.resolved-total {
    width: 13rem;
}

.assigned-today {
    width: 13rem;
}

.resolved-today {
    width: 13rem;
}

.statistics {
    display: flex;
    flex-direction: row;
    width: 70%;
}

.data-container {
    display: flex;
    flex-direction: column;
}

@media (max-width: 1440px) {
    .statistics {
        width: 90%;
    }

    .doughnut {
        width: 65rem;
    }

    .bar {
        width: 70rem;
    }
}

@media (max-width: 1024px) {
    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        display: flex;
        flex-direction: column;
    }

    .doughnut {
        width: 150%;
    }

    .bar {
        width: 250%;
    }

}

@media (max-width: 768px) {
    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }

}

@media (max-width: 425px) {
    .main-content {
        margin-left: 12rem;
    }

    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        width: 250%;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }

    .data-container {
        display: flex;
        flex-direction: row;
    }

    .data-bottom {
        display: flex;
        flex-direction: column;
    }

    .data-top {
        display: flex;
        flex-direction: column;
    }

}

@media (max-width: 375px) {
    .main-content {
        margin-left: 13rem;
    }

    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        width: 280%;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }
}

@media (max-width: 320px) {
    .main-content {
        margin-left: 15rem;
    }

    .detail-container {
        display: flex;
        flex-direction: column;
    }

    .statistics {
        width: 300%;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 200%;
    }
}
</style>