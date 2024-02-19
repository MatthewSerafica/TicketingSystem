<template>
    <section class="d-flex vh-100 justify-content-center align-items-center">
        <div class="d-flex flex-column justify-content-center align-items-center h-custom">
            <div class="d-flex flex-row justify-content-center align-items-center gap-5">
                <div class="">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form @submit.prevent="login">
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <div class="input-group">
                                <span class="input-group-text" id="email-addon"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" id="email" class="form-control p-2" @keydown.space.prevent
                                    v-model="form.email" placeholder="Enter email address" aria-label="Username"
                                    aria-describedby="email-addon" />
                            </div>
                            <span v-if="form.errors.email"
                                class="text-danger">
                                {{ form.errors.email }}</span>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-3">
                            <div class="input-group">
                                <span class="input-group-text" id="email-addon"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" id="form3Example4" class="form-control p-2"
                                    placeholder="Enter password" v-model="form.password" />
                            </div>
                            <span v-if="form.errors.password"
                                class="text-danger">
                                {{ form.errors.password }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center ms-2">
                            <!-- Checkbox -->
                            <a href="#!" class="text-body">Forgot password?</a>
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                            <!-- <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="#!"
                                    class="link-danger">Register</a></p> -->
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import 'bootstrap-icons/font/bootstrap-icons.css';
import { useForm, Link } from '@inertiajs/vue3'


const form = useForm({
    email: '',
    password: '',
})
const login = () => form.post(route("login.store"));
</script>

<style>
.divider:after,
.divider:before {
    content: "";
    flex: 1;
    height: 1px;
    background: #eee;
}

.h-custom {
    height: calc(100% - 73px);
}


@media (max-width: 450px) {
    .h-custom {
        height: 100%;
    }
}
</style>