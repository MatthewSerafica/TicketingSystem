<template>
    <div>
        <Header></Header>
        <div class="mt-5 pt-5">
            <form @submit.prevent="create">
                <br />
                <div class="d-flex flex-column justify-content-center align-items-center gap-4">
                    <div class="title-container fw-bold text-center">
                        <h1>Create Users</h1>
                    </div>
                    <div class="d-flex flex-column">
                        <label for="user_type" class="fw-semibold">User Type</label>
                        <select id="user_type" class="form-select h-100 rounded border-secondary-subtle"
                            placeholder="Select User Type..." v-model="form.user_type">
                            <option disabled>Select User Type</option>
                            <option value="employee">Employee</option>
                            <option value="technician">Technician</option>
                        </select>
                    </div>
                    <div class="d-flex flex-row gap-3 justify-content-center">
                        <div class="d-flex flex-column">
                            <label for="name" class="fw-semibold">Name</label>
                            <input id="name" class="form-control rounded border-secondary-subtle" type="text"
                                placeholder="First and Last Name..." v-model="form.name" />
                        </div>
                        <div class="d-flex flex-column">
                            <label for="email" class="fw-semibold">Email</label>
                            <input id="email" class="form-control h-100 rounded border-secondary-subtle" type="email"
                                placeholder="Enter Email..." v-model="form.email" />
                        </div>
                    </div>
                    <div class="d-flex flex-row gap-3 justify-content-center">
                        <div class="d-flex flex-column">
                            <label for="password" class="fw-semibold">Password</label>
                            <input id="password" class="form-control rounded border-secondary-subtle" type="password"
                                placeholder="Enter Password..." v-model="form.password" />
                        </div>
                        <div class="d-flex flex-column">
                            <label for="conf" class="fw-semibold">Confirm Password</label>
                            <input id="conf" class="form-control h-100 rounded border-secondary-subtle" type="password"
                                placeholder="Confirm Password..." v-model="form.conf" />
                        </div>
                    </div>
                    <div v-if="form.user_type === 'employee'" class="d-flex flex-row gap-3 justify-content-center">
                        <div class="d-flex flex-column">
                            <label for="department" class="fw-semibold">Department</label>
                            <select id="department" class="form-select h-100 rounded border-secondary-subtle"
                                placeholder="Select Department..." v-model="form.department">
                                <option disabled>Select Department</option>
                                <option v-for="department in departments" :value="department.department">{{
                                    department.department }}
                                </option>
                            </select>
                        </div>
                        <div class="d-flex flex-column">
                            <label for="office" class="fw-semibold">Office</label>
                            <select id="office" class="form-select h-100 rounded border-secondary-subtle"
                                placeholder="Select Department..." v-model="form.office">
                                <option disabled>Select Office</option>
                                <option v-for="office in offices" :value="office.office">{{
                                    office.office }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div v-if="form.user_type === 'technician'" class="d-flex flex-row gap-3 justify-content-center">
                        <div class="d-flex flex-column">
                            <label for="assigned" class="fw-semibold">Assign a department</label>
                            <select id="assigned" class="form-select h-100 rounded border-secondary-subtle"
                                placeholder="Select Department..." v-model="form.assigned">
                                <option disabled>Select Department</option>
                                <option v-for="department in departments" :value="department.department">{{
                                    department.department }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="container w-25">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-center gap-2">
                                <Button :name="'Submit'" :color="'primary'" class="submit-btn"></button>
                                <Link :href="`/admin/users`" class=" btn btn-outline-primary">Cancel</Link>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, useForm} from "@inertiajs/vue3";
import Button from '@/Components/Button.vue';
import { defineProps, ref, watch } from 'vue'; 

const props = defineProps({
    departments: Object,
    offices: Object,
})

const form = useForm({
    user_type: null,
    name: null,
    email: null,
    password: null,
    conf: null,
    department: null,
    office: null,
    assigned: null,
})


const create = () => form.post(route('admin.users.store'), { preserveScroll: false, preserveState: false })

watch(() => form.name, (newValue) => {
    // Check if the name contains a space
    const spaceIndex = newValue.indexOf(' ');
    if (spaceIndex !== -1) {
        // Split the name into first and last names
        const firstName = newValue.substring(0, spaceIndex);
        const lastName = newValue.substring(spaceIndex + 1);
        // Update the form fields with first and last names
        form.firstName = firstName;
        form.lastName = lastName;
    } else {
        // If no space is found, assume the entire input is the first name
        form.firstName = newValue;
        form.lastName = '';
    }
});
</script>