<template>
    <div class="custom-modal" @close="closeDelete">
        <div class="modal-content d-flex flex-column">
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
                        <input type="text" class="p-2 px-4 rounded form-control" v-model="form.title" placeholder="Title">
                        <textarea class="w-full p-4 rounded form-control"
                            placeholder="What are you thinking about?" v-model="form.content"></textarea>
                    </div>
                    <div class="d-flex justify-content-end py-1 px-3 border-gray-subtle">
                        <Button :name="'Post'" :color="'primary'" :width="'25'"></Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits } from 'vue';
import Button from '@/Components/Button.vue';
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['closeDelete'])

const closeDelete = () => {
    emit('closeDelete')
}

const form = useForm({
    title: null,
    content: null,
    tagged_user: null,
})

const post = () => form.post(route('technician.forum.store'), { preserveScroll: false, preserveState: false })
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
}

.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
}
</style>