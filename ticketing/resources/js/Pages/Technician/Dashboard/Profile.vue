<template>
    <div class="whole-content">
        <Header class="sticky-top" style="z-index: 110;"></Header>
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
            <Link :href="route('technician')"
                class="btn btn-secondary m-2 d-flex flex-row justify-content-start align-items-center back-button"
                style="width: 6rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                class="bi bi-caret-left-fill" viewBox="0 0 16 16">
                <path
                    d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z" />
            </svg>
            <span>Back</span>
            </Link>
        </div>
        <div class="d-flex flex-column gap-4 justify-content-center align-items-center mt-3 mb-3 main-content">
            <div class="d-flex flex-column flex-md-row gap-5 justify-content-between align-items-center">
                <div class="card shadow p-2 user-container">
                    <div class="card-body d-flex flex-column flex-md-row gap-5 align-items-center">
                        <div class="d-flex flex-column justify-content-center align-items-center mb-3">
                            <div class="mb-2 text-center">
                                <!-- AVATAR -->
                                <input @change="handleFileChange" ref="fileInput" type="file" hidden>
                                <img v-if="profilePictureUrl || users.avatar !== 'http://127.0.0.1:8000/storage'"
                                    @click="openFileInput" :src="profilePictureUrl ?? users.avatar"
                                    alt="User profile picture" class="avatar rounded-circle shadow">
                                <EmptyProfile v-else @click="openFileInput" class="avatar rounded-circle shadow">
                                </EmptyProfile>
                            </div>
                            <div>
                                <span v-if="!selectedInput || selectedInput !== users.name"
                                    class="card-text fw-semibold" @click="showInput(users.name, users.id)">
                                    {{ users.name }}
                                </span>
                                <input type="text" v-if="selectedInput === users.name" v-model="editData[users.name]"
                                    @blur="updateData(users.name, users.id, 'name', false, false)"
                                    @keyup.enter="updateData(users.name, users.id, 'name', false, false)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <p class="card-text text-capitalize">{{ users.user_type }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center gap-2" style="flex: 1;">
                            <div class="card-title fw-bold d-flex flex-row align-items-center gap-3">
                                <h3 class="mt-1">User Details</h3>
                                <span v-if="users.technician.is_working == 1" class="badge bg-success rounded-circle"
                                    style="width: 2em; height: 2em;"><span
                                        class="visually-hidden">Available</span></span>
                                <span v-if="users.technician.is_working == 0" class="badge bg-danger rounded-circle"
                                    style="width: 2em; height: 2em;"><span
                                        class="visually-hidden">Unavailable</span></span>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <div class="card-subtitle fw-medium fs-5">
                                        Status
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn text-start"
                                            :class="users.technician.is_working ? 'N/A' : 'N/A'">
                                            {{ users.technician.is_working ? 'Available' : 'Unavailable' }}
                                        </button>
                                        <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-item disabled">Select status</li>
                                            <li class="btn dropdown-item" @click="updateStatus(0)">Unavailable</li>
                                            <li class="btn dropdown-item" @click="updateStatus(1)">Available</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mb-2">
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
                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <div class="card-subtitle fw-medium fs-5 d-flex flex-row gap-2">
                                            <span>Assigned Department</span>
                                            <div class="d-flex flex-grow-1 gap-2 w-100 align-items-center">
                                                <button v-if="!isEditable" class="btn btn-outline-primary"
                                                    @click="handleEdit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button v-if="isEditable" class="btn btn-outline-primary"
                                                    @click="handleEdit">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center align-items-start">
                                            <div v-for="(assignedDep, index) in users.technician.departments"
                                                :key="index">
                                                <div>
                                                    <div v-if="!isEditable">
                                                        <div v-for="dep in assignedDep.departments"
                                                            class="d-flex flex-row justify-content-center align-items-center">
                                                            {{ dep.department }}
                                                        </div>
                                                    </div>
                                                    <div class="btn-group position-static" v-if="isEditable">
                                                        <button type="button"
                                                            class="btn dropdown-toggle dropdown-toggle-split"
                                                            data-bs-toggle="dropdown" aria-expanded="false"
                                                            data-bs-reference="parent">
                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                        </button>
                                                        <div v-for="dep in assignedDep.departments"
                                                            class="d-flex flex-row justify-content-center align-items-center">
                                                            <button type="button" class="btn text-start tech-btn"
                                                                @click="toggleTechnicianCTAs">
                                                                {{ dep ? dep.department : 'N/A' }}
                                                            </button>
                                                            <button v-if="technicianCTAs"
                                                                class="btn align-items-center justify-content-center d-flex text-danger fs-5"
                                                                style="height:1.5em;"
                                                                @click="removeDropdown(users.technician.departments, index, users.technician.technician_id, dep.id)"><i
                                                                    class="bi bi-dash-circle-fill"></i>
                                                            </button>
                                                        </div>
                                                        <ul class="dropdown-menu"
                                                            style="max-height: 300px; overflow-y: auto; width: 14rem;">
                                                            <!--All-->
                                                            <li class="dropdown-item disabled">Select a department</li>
                                                            <li v-for="department in departments"
                                                                class="btn dropdown-item"
                                                                @click="assignDepartment(users, index, department.id, users.technician.technician_id)">
                                                                {{ department.department }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <button v-if="technicianCTAs && isEditable"
                                                class="btn align-items-center justify-content-center d-flex text-primary fs-5"
                                                style="height:1.5em;"
                                                @click="addDropdown(users.technician.departments)">
                                                <i class="bi bi-plus-circle-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gap-3 data-container">
                    <div class="d-flex flex-column flex-md-row gap-3 mb-2">
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
                                        <div class="fw-semibold fs-4">{{ complexity ? complexity.simple : '0' }}

                                            <span class="fw-normal text-secondary fs-6">Simple</span>
                                        </div>
                                        <div class="fw-semibold fs-4">{{ complexity ? complexity.complex : '0' }}

                                            <span class="fw-normal text-secondary fs-6">Complex</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-3">
                        <div class="assigned-today card border-3 border-danger p-2 shadow"
                            style="border-top: 1px; border-left: 1px; border-right: 1px;">
                            <div class="card-body d-flex flex-column gap-4">
                                <div class="d-flex flex-column gap-3">
                                    <div class="fs-5 text-secondary">
                                        Assigned (Today)
                                    </div>
                                    <div class="fw-semibold fs-2 ">{{ assigned_today ? assigned_today : '0' }}

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
                                    <div class="fw-semibold fs-2">{{ resolved_today ? resolved_today : '0' }}

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
                                    <div class="fw-semibold fs-3">{{ time ? time : '0' }}

                                        <span class="fw-normal text-secondary fs-6">Hour(s)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-4 statistics">
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
import Header from '@/Pages/Layouts/TechnicianHeader.vue';
import Bar from '@/Pages/Technician/Dashboard/Charts/Bar.vue';
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import Doughnut from '@/Pages/Technician/Dashboard/Charts/Doughnut.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Alpine from 'alpinejs';
import { reactive, ref, watchEffect } from 'vue';
import axios from 'axios';

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
    service: Object,
    yearly: Object,
    assigned_today: Object,
    resolved_today: Object,
    time: Object,
    complexity: Object,
})

let isEditable = ref(false);

const handleEdit = () => {
    if (!isEditable.value) {
        isEditable.value = true;
        technicianCTAs.value = true;
    } else {
        isEditable.value = false;
        if (technicianCTAs.value) {
            technicianCTAs.value = false;
        }
    }
}

let technicianCTAs = ref(false);

const toggleTechnicianCTAs = () => {
    if (technicianCTAs.value) {
        technicianCTAs.value = false;
    } else {
        technicianCTAs.value = true;
    }
    console.log(technicianCTAs.value)
};


const addDropdown = (user) => {
    user.push({
        department_id: null,
        technician: [],
    })
    console.log(user.value)
}

const removeDropdown = async (user, assignedIndex, techId, depId) => {
    console.log(user)
    user.splice(depId, 1)
    removeData(depId, techId)
}

const removeData = async (data, id) => {
    try {
        const form = useForm({
            department_id: data,
            technician: id,
        })
        await form.delete(route('technician.profile.remove.department'))
    } catch (error) {
        console.error('Error removing data:', error)
    }
}
const replaceTechnician = async (id, technician, old, assignId) => {
    try {
        const form = useForm({
            department_id: id,
            technician: technician,
            old: old,
            assignId: assignId,
        })
        await form.put(route('technician.profile.replace.department'))
    } catch (error) {
        console.error('Error replacing data:', error);
    }
}

const assignDepartment = async (user, assignedIndex, departmentId, technician) => {
    if (user.technician.departments[assignedIndex].departments && user.technician.departments[assignedIndex].departments.length > 0) {
        console.log('if', user.technician.departments[assignedIndex].departments[0].id)
        const old = user.technician.departments[assignedIndex].departments[0].id
        const assignId = user.technician.departments[assignedIndex].id
        await replaceTechnician(departmentId, technician, old, assignId)
    } else {
        console.log('else')
        await showInput(departmentId)
        await updateData(departmentId, technician, 'department_id', true)
    }
};

let selectedInput = ref(null);
let editData = reactive({});

const showInput = (data) => {
    selectedInput.value = data;
    editData[data] = data ? data : '';
}


const updateStatus = (is_working) => {
    const form = useForm({
        is_working: is_working,
    });

    form.put(route('technician.update.status', { is_working: is_working }));
}

const fileInput = ref(null);

const openFileInput = () => {
    fileInput.value.click();

}
const updateData = async (data, id, updateField, isTechnician) => {
    if (selectedInput.value === data) {
        const routeName = isTechnician ? 'technician.tech.update' : 'technician.update';
        const form = useForm({
            [updateField]: editData[data],
        });
        console.log(routeName)

        await form.put(route(routeName, { user_id: id, field: updateField }));

        selectedInput.value = null;
        editData[data] = '';
    }
};

const profilePictureUrl = ref(null);

const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        console.log('No File')
        return;
    }
    profilePictureUrl.value = URL.createObjectURL(file);

    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const response = await axios.post(route('technician.profile.avatar', props.users.id), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        if (response.status === 200) {
            page.props.flash.success = response.data.success;
            page.props.flash.message = response.data.message;
            showSuccessToast.value = true;
        } else {
            page.props.flash.error = true;
            page.props.flash.error_message = response.data.message || 'Unexpected error occurred';
            showErrorToast.value = true;
        }
    } catch (error) {
        console.error('Error during file upload:', error);
        page.props.flash.error = true;
        if (error.response?.data?.message) {
            page.props.flash.error_message = error.response.data.message;
        } else if (error.response?.status === 500) {
            page.props.flash.error_message = 'Internal server error occurred during file upload.';
        } else {
            page.props.flash.error_message = 'File upload failed. Please try again.';
        }
        showErrorToast.value = true;
    }
};
</script>

<style scoped>
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

.back-button {
    width: 6rem;
    transition: transform 0.3s ease;
}

.back-button:hover {
    transform: scale(1.1);
}

.avatar {
    width: 10rem;
    height: 10rem;
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}

.avatar:hover {
    opacity: 0.95;
    border-color: blue;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
    }

    100% {
        opacity: 1;
    }
}

.statistics {
    display: flex;
    flex-direction: row;
    width: 100%;
}

.doughnut {
    width: 25rem;
}

.bar {
    width: 50rem;
}

.assigned-total,
.resolved-total,
.assigned-today,
.resolved-today {
    width: 13rem;
}


@media (max-width: 768px) {

    .doughnut,
    .bar {
        width: 90%;
    }

}
</style>