<template>
    <div >
        <Header class="sticky-top" ></Header>
       
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
            <div class="back-button">
                <Link :href="route('observer')"
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
            <div class="container">
            <div class="d-flex flex-column gap-4 justify-content-center align-items-center mt-3 mb-3 profile-details">
                <div class="d-flex gap-5 justify-content-between align-items-center">
                    <div class="card shadow p-2">
                        <div class="card-body d-flex flex-row gap-5 align-items-center">
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
                                    <h8 class="card-text fw-semibold">{{ users.name }}</h8>
                                </div>
                                <div>
                                    <p class="card-text text-capitalize">{{ users.user_type }}</p>
                                </div>
                            </div>

                            <div class="d-flex flex-column justify-content-center gap-2">
                                <div class="card-title fw-bold d-flex flex-row align-items-center gap-3">
                                    <h3 class="mt-1">User Details</h3>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <div class="mb-2">
                                        <div class="card-subtitle fw-medium fs-5">
                                            Email
                                        </div>
                                        <h8 class="card-text">{{ users.email }}</h8>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </div>
</template>

<script setup>
import Toast from '@/Components/Toast.vue';
import Header from '@/Pages/Layouts/ObserverHeader.vue';
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import { Link, usePage } from '@inertiajs/vue3';
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
    service: Object,
    yearly: Object,
})

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
        const response = await axios.post(route('admin.profile.avatar', props.users.id), formData, {
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
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.back-button {
    width: fit-content; /* Adjusts width based on content */
    transition: transform 0.3s ease;
}

.back-button:hover {
    transform: scale(1.1);
}

.avatar {
    width: 100%;
    max-width: 10rem;
    height: auto;
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}

.avatar:hover {
    opacity: 0.95;
    border-color: blue;
}

.toast-container {
    position: absolute;
    top: 20px;
    right: 20px;
}

.profile-details {
    margin-top: 20px;
}

.card {
    width: 100%;
}

.avatar-section, .details-section {
    flex: 1; /* Distribute available space equally */
    min-width: 0; /* Allow contents to shrink */
}

@media screen and (max-width: 768px) {
    .card-body {
        flex-direction: column;
    }
}

</style>