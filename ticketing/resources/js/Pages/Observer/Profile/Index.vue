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
            <Link :href="route('observer')"
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
            <div class="d-flex gap-5 justify-content-between align-items-center detail-container">
                <div class="card shadow p-2 user-container">
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

                        <div class="d-flex flex-column justify-content-center gap-2" style="flex: 1;">
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

let selectedInput = ref(null);
let editData = reactive({});

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

    .data-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Center items horizontally */
        align-items: center;
        /* Center items vertically */
        margin-top: 3rem;
        /* Adjust margin top as needed */
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

    .data-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Center items horizontally */
        align-items: center;
        /* Center items vertically */
        margin-top: 3rem;
        /* Adjust margin top as needed */
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

    .data-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Center items horizontally */
        align-items: center;
        /* Center items vertically */
        margin-top: 3rem;
        /* Adjust margin top as needed */
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

    .data-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Center items horizontally */
        align-items: center;
        /* Center items vertically */
        margin-top: 3rem;
        /* Adjust margin top as needed */
    }


    .detail-container {
        display: flex;
        flex-direction: column;
        width: 50%;
    }


    .statistics {
        width: 300%;
    }

    .doughnut {
        width: 100%;
    }

    .bar {
        width: 100%;
    }
}
</style>