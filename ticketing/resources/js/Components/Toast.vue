<template>
    <div>
        <b-toast v-model="showToast" :auto-hide-delay="5000" no-close-button>
            <div class="toast-header">
                <strong class="me-auto">SR No is required for Resolved Status</strong>
                <button type="button" class="btn-close" @click="showToast = false"></button>
            </div>
            <div class="toast-body">
                {{ errorMessage }}
            </div>
        </b-toast>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const showToast = ref(false);
const errorMessage = ref('');
</script>