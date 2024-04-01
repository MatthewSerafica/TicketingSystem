<template>
    <div>
    <section class="d-flex vh-100 justify-content-center align-items-center">
        <div class="d-flex flex-column justify-content-center align-items-center h-custom">
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-5">
                <div class="col-md-6">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="card p-4 col-md-5">
                    <div class="card-body">
                    <h3 class="mb-5 text-primary text-center">TMDD Ticketing System</h3>
                    <form @submit.prevent="login">
                        <!-- Email input -->
                        <div class="form-outline mb-4">
                            <div class="input-group">
                                <span class="input-group-text" id="email-addon"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" id="email" class="form-control p-2 " @keydown.space.prevent
                                    v-model="form.email" placeholder="Enter email address" aria-label="Username"
                                    aria-describedby="email-addon" />
                            </div>
                            <span v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</span>
                        </div>

                        <!-- Password input -->
                        <div class="form-outline mb-3">
                            <div class="input-group">
                                <span class="input-group-text" id="email-addon"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" id="form3Example4" class="form-control p-2"
                                    placeholder="Enter password" v-model="form.password" />
                            </div>
                            <span v-if="form.errors.password" class="text-danger">{{ form.errors.password }}</span>
                        </div>

                         
                        <Button :name="'Login'" :color="'primary'" :width="'100'" :height="'50'"></Button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <Footer style="position: fixed; bottom: 0; width: 100%;"></Footer>
    </div>
</template>

<script setup>
import 'bootstrap-icons/font/bootstrap-icons.css';
import { useForm, Link } from '@inertiajs/vue3'
import Footer from '@/Pages/Layouts/Footer.vue'
import Button from '@/Components/Button.vue'

const form = useForm({
    email: '',
    password: '',
})
const login = () => form.post(route("login.store"));
</script>

<style>

</style>
