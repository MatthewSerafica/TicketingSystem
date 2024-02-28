<template>
    <div>
        <Header></Header>
        <div class="d-flex flex-row align-items-center justify-content-center mt-5">
            <div class="card w-25">
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
                                <span v-if="!selectedNameInput || selectedNameInput !== user.name" class="card-text"
                                    @click="showNameInput(user.name, user.id)">{{ user.name
                                    }}</span>
                                <input type="text" v-if="selectedNameInput === user.name" v-model="editName[user.name]"
                                    @blur="updateName(user.name, user.id)" @keyup.enter="updateName(user.name, user.id)"
                                    class="rounded border border-secondary-subtle text-start">
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Email
                                </div>
                                <p class="card-text">{{ user.email }}</p>
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    User Type
                                </div>
                                <p class="card-text text-capitalize">{{ user.user_type }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Department
                                </div>
                                <p class="card-text">{{ user.employee.department }}</p>
                            </div>
                            <div>
                                <div class="card-subtitle fw-medium fs-5">
                                    Office
                                </div>
                                <p class="card-text">{{ user.employee.office }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
            </div>
        </div>
    </div>
</template>

<script setup>
import Header from '@/Pages/Layouts/AdminHeader.vue'
import { useForm } from '@inertiajs/vue3';
import { reactive, ref, onMounted } from 'vue';

const props = defineProps({
    employee: Object,
    user: Object,
})

let selectedNameInput = ref(null);
let editName = reactive({});

const showNameInput = (name, id) => {
    selectedNameInput.value = name;
    editName[name] = name ? name : '';
}

const updateName = async (name, id) => {
    if (selectedNameInput.value === name) {
        const form = useForm({
            name: editName[name],
        });

        await form.put(route('admin.users.update.name', { user_id: id }));

        selectedNameInput.value = null;
        editName[name] = '';
    }
};
</script>