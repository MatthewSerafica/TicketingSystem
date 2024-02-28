<template>
    <div class="">
        <div :class="borderColor(success, error)"
            class="border px-3 py-2 d-flex flex-row rounded justify-content-between gap-3 align-items-center">
            <svg v-if="success" xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="lightgreen"
                class="bi bi-check2-circle" viewBox="0 0 16 16">
                <path
                    d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0" />
                <path
                    d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="gold"
                class="bi bi-exclamation-diamond" viewBox="0 0 16 16">
                <path
                    d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.48 1.48 0 0 1 0-2.098zm1.4.7a.495.495 0 0 0-.7 0L1.134 7.65a.495.495 0 0 0 0 .7l6.516 6.516a.495.495 0 0 0 .7 0l6.516-6.516a.495.495 0 0 0 0-.7L8.35 1.134z" />
                <path
                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
            </svg>
            <div class="d-flex flex-column">
                <div class="d-flex flex-row gap-5 justify-content-between">
                    <strong v-if="success" class="me-auto text-light">{{ success }}</strong>
                    <strong v-else class="me-auto text-light">{{ error }}</strong>
                </div>
                <div v-if="success" class="text-start text-light">
                    {{ message }}
                </div>
                <div v-else class="text-start text-light">
                    {{ error_message }}
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="closeToast"></button>
        </div>
    </div>
</template>

<script setup>

const emit = defineEmits()

const props = defineProps(['success', 'error', 'message', 'error_message']);

const borderColor = (success, error) => {
    if (success) {
        return 'bg-success'
    } else if (error) {
        return 'bg-danger'
    }
}

const closeToast = () => {
    emit('close');
}

</script>