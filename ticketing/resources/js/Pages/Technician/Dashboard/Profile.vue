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
        <div class="container justify-content-center mt-3 align-items-center mb-3">
            <div class="row row-cols-2">
                <div class="card shadow p-2 w-25">
                    <div class="card-body d-flex flex-column gap-1">
                        <div class="d-flex flex-column justify-content-center align-items-center mb-3">
                            <div class="mb-2 text-center">
                                <!-- AVATAR -->
                                <input @change="handleFileChange" ref="fileInput" type="file" hidden>
                                <img v-if="profilePictureUrl || users.avatar !== 'http://127.0.0.1:8000/storage'" @click="openFileInput" :src="profilePictureUrl ?? users.avatar" alt="User profile picture"
                                    class="avatar rounded-circle shadow">
                                <EmptyProfile v-else @click="openFileInput"
                                    class="avatar rounded-circle shadow"></EmptyProfile>
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
                        <div class="d-flex flex-column justify-content-center gap-2">
                            <div class="card-title fw-bold d-flex flex-row align-items-center gap-4">
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
                                        <div class="card-subtitle fw-medium fs-5">
                                            Assigned Department
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn text-start">
                                                {{ users.technician.assigned_department ?
                    users.technician.assigned_department : 'Unassigned' }}
                                            </button>
                                            <button type="button" class="btn dropdown-toggle dropdown-toggle-split"
                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                data-bs-reference="parent">
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
                                    <div class="d-flex flex-row gap-5">
                                        <div class="d-flex flex-column">
                                            <div class="card-subtitle fw-medium fs-6">
                                                Total Assigned
                                            </div>
                                            <p class="card-text text-center">{{ users.technician.tickets_assigned }}</p>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <div class="card-subtitle fw-medium fs-6">
                                                Total Resolved
                                            </div>
                                            <p class="card-text text-center">{{ users.technician.tickets_resolved }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-row align-items-center justify-content-center card-row w-75">
                    <div
                        class="flex-grow-1 d-flex flex-column justify-content-center align-items-center card p-5 gap-5 shadow">
                        <div class="">
                            <Doughnut :service="service" style="width: 30rem;"></Doughnut>
                        </div>
                        <div class="">
                            <Bar :yearly="yearly" style="width: 45rem;"></Bar>
                        </div>
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
})

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
    /* Width of the avatar */
    height: 10rem;
    /* Height of the avatar */
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}

.avatar:hover {
    opacity: 0.90;
    border-color: blue;
}
</style>