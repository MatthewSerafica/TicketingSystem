<template>
    <div class="custom-modal" @close="closeDelete">
        <div class="modal-content d-flex flex-column overflow-y-auto">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex justify-content-center w-100">
                    <h4>Create Post</h4>
                </div>
                <div class="d-flex flex-row justify-content-end">
                    <button type="button" class="btn-close btn btn-secondary rounded-circle p-2" data-bs-dismiss="modal"
                        aria-label="Close" @click="closeDelete"></button>
                </div>
            </div>
            <div>
                <form @submit.prevent="post">
                    <div class="p-4 d-flex flex-column gap-2">
                        <input type="text" class="p-2 px-4 rounded form-control" v-model="form.title"
                            placeholder="Title">
                        <textarea class="w-full p-4 rounded form-control" placeholder="What are you thinking about?"
                            v-model="form.content"></textarea>
                        <div v-if="isUploadImageShown" class="card p-2 rounded border">
                            <div class="position-absolute" style="right: 10px; top: 10px; z-index: 100;">
                                <button type="button" class="btn-close btn btn-secondary rounded-circle p-2"
                                    data-bs-dismiss="modal" aria-label="Close" @click="closeUploadImage"></button>
                            </div>
                            <div v-if="!uploadedPicture"
                                class="d-flex flex-column justify-content-center align-items-center btn text-secondary upload"
                                @click="openFileInput">
                                <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="35" fill="currentColor"
                                        class="bi bi-image" viewBox="0 0 16 16">
                                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                        <path
                                            d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
                                    </svg>
                                </div>
                                <span>Add Photos</span>
                            </div>
                            <div v-if="uploadedPicture" class="card">
                                <img :src="uploadedPicture" alt="" @click="openFileInput" class="rounded image">
                            </div>
                        </div>
                        <input @change="handleFileChange" ref="fileInput" type="file" hidden>
                    </div>
                    <div class="d-flex py-1 px-3 border-gray-subtle"
                        :class="{ 'justify-content-between': !isUploadImageShown, 'justify-content-end': isUploadImageShown }">
                        <button v-if="!isUploadImageShown" type="button" as="button" title="Add Photo"
                            class="btn rounded-4 btn-outline-secondary" @click="showUploadImage">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="35" fill="currentColor"
                                class="bi bi-image" viewBox="0 0 16 16">
                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                <path
                                    d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
                            </svg>
                        </button>
                        <Button :name="'Post'" :color="'primary'" :width="'25'"></Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits, ref } from 'vue';
import Button from '@/Components/Button.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';


const page = usePage();

let showSuccessToast = ref(false);
let showErrorToast = ref(false);

const emit = defineEmits(['closeDelete'])

const closeDelete = () => {
    emit('closeDelete')
}

let isUploadImageShown = ref(false);

function closeUploadImage() {
    if (isUploadImageShown.value) {
        isUploadImageShown.value = false;
        uploadedPicture.value = null;
    }
}

function showUploadImage() {
    if (!isUploadImageShown.value) {
        isUploadImageShown.value = true;
        uploadedPicture.value = null;
    }
}

const fileInput = ref(null);

const openFileInput = () => {
    fileInput.value.click();

}

const formData = new FormData();

const form = useForm({
    title: null,
    content: null,
    tagged_user: null,
})

const post = async () => {
    // Append form data to FormData
    formData.append('title', form.title);
    formData.append('content', form.content);
    formData.append('tagged_user', form.tagged_user ?? '');

    try {
        // Post form data along with image
        const response = await axios.post(route('technician.forum.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // Handle response
        console.log(response);

        if (response.status === 200) {
            page.props.flash.success = response.data.success;
            page.props.flash.message = response.data.message;
            showSuccessToast.value = true;
        } else {
            page.props.flash.error = true;
            page.props.flash.error_message = response.data.message || 'Unexpected error occurred';
            showErrorToast.value = true;
        }

        closeDelete();
        window.location.reload();
    } catch (error) {
        // Handle error
        console.error('Error during form submission:', error);
        page.props.flash.error = true;
        if (error.response?.data?.message) {
            page.props.flash.error_message = error.response.data.message;
        } else if (error.response?.status === 500) {
            page.props.flash.error_message = 'Internal server error occurred during form submission.';
        } else {
            page.props.flash.error_message = 'Form submission failed. Please try again.';
        }
        showErrorToast.value = true;
    }
};



const uploadedPicture = ref(null);

const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        console.log('No File')
        return;
    }
    uploadedPicture.value = URL.createObjectURL(file);

    formData.append('image', file);
};
</script>

<style scoped>
.custom-modal {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 100;
}

.modal-content {
    background-color: #fff;
    padding-top: 15px;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 20px;
    border-radius: 8px;
    text-align: center;
    width: 40%;
    max-height: 70%;
}

.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
}

.upload:hover {
    background-color: #F2F1F1;
}

.image {
    transition: transform 1s ease;
    cursor: pointer;
}


.image:hover {
    opacity: 0.95;
    border-color: blue;
}
</style>