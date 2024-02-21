<template>
    <nav class="navbar navbar-expand-lg shadow-sm header-color">
        <div class="container-fluid gap-3">
            <div class="d-flex gap-2 col-8">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-brilliance mt-2" viewBox="0 0 16 16">
                    <path
                        d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16M1 8a7 7 0 0 0 7 7 3.5 3.5 0 1 0 0-7 3.5 3.5 0 1 1 0-7 7 7 0 0 0-7 7" />
                </svg>
                <a class="navbar-brand text-white" href="/technician">TMDD Ticketing System</a>
            </div>
            <div class="" id="navbarNav">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item">
                        <a class="nav-link text-white" aria-current="page" href="/technician">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/technician/tickets">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/technician/service-report">Service Reports</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex gap-2 pe-5 me-5 justify-content-center align-items-center">
                <i class="bi bi-bell text-white me-3"  style="font-size: 20px;"></i> 
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                </svg>
                <div class="dropdown-center">
                    <a class="text-decoration-none dropdown-toggle text-white" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{ page.props.user.name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <Link :href="route('logout')" method="delete" v-if="page.props.user"
                                class="text-decoration-none dropdown-item">Logout
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
</script>

<style>
.header-color {
    background-color: #063970;
}
</style>