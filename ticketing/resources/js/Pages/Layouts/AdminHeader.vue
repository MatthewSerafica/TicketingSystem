<template>
    <div>
    <nav class="navbar navbar-expand-lg shadow-sm h-color text-white">
        <div class="container-fluid gap-3">
            <div class="d-flex gap-2 col-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-brilliance mt-2" viewBox="0 0 16 16">
                    <path
                        d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16M1 8a7 7 0 0 0 7 7 3.5 3.5 0 1 0 0-7 3.5 3.5 0 1 1 0-7 7 7 0 0 0-7 7" />
                </svg>
                <a class="navbar-brand text-white" href="/admin">TMDD Ticketing System</a>
            </div>
        </div>
            <div class="" id="navbarNav">
                <ul class="navbar-nav ">
                    <li class="nav-item">
                        <a class="nav-link text-white" aria-current="page" href="/admin">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/admin/tickets">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/admin/users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/admin/department">Department</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex gap-2 pe-5 me-5 justify-content-center align-items-center">
                <button class="btn p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificationBar"
                    aria-controls="notificationBar">
                    <i class="bi bi-bell text-white me-3" style="font-size: 20px;"></i>
                    <span class="text-light bg-danger position-absolute top-0 rounded-pill badge" id="count"
                        style="font-size: small; padding: 2px 5px 2px 5px;"></span>
                </button>
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
                <div class="d-flex gap-2 pe-5 me-5 justify-content-center align-items-center">
                    <button class="btn p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificationBar"
                        aria-controls="notificationBar">
                        <i class="bi bi-bell text-white me-3" style="font-size: 20px;"></i>
                        <span class="text-light bg-danger position-absolute top-0 rounded-pill badge" id="count"
                            style="font-size: small; padding: 2px 5px 2px 5px;">{{ notificationCount }}</span>
                    </button>
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

        <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationBar" aria-labelledby="notificationBarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="notificationBarLabel">Notifications</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="container mt-4">
                    <nav class="nav nav-pills nav-fill navbar-light" style="background-color: #fafafa;">
                        <a class="nav-link" :class="{ 'active': activeTab === 'technician' }" aria-current="page"
                            href="#technician-content" @click.prevent="showTab('technician')">Technician</a>
                        <a class="nav-link" :class="{ 'active': activeTab === 'employee' }" href="#employee-content"
                            @click.prevent="showTab('employee')">Employee</a>
                    </nav>
                    <div id="tab-content">
                        <div v-if="activeTab === 'technician'" class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">Notification Title</h5> <!--PLACEHOLDER-->
                                <p class="card-text">Technician is going on a vacation</p> <!--PLACEHOLDER-->
                                <small class="card-text fst-italic text-muted">{{ notificationDateTime() }}</small>
                            </div>
                        </div>
                        <div v-if="activeTab === 'employee'" class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">Notification Title</h5><!--PLACEHOLDER-->
                                <p class="card-content">Employee is having a bachelors party.</p> <!--PLACEHOLDER-->
                                <small class="card-text fst-italic text-muted">{{ notificationDateTime() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from "@inertiajs/vue3";
const activeTab = ref('technician'); // Default active tab
const page = usePage();

function notificationDateTime() {
    const currentDateTime = new Date();
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return currentDateTime.toLocaleDateString('en-US', options).replace(/\//g, '-');
}

function showTab(tab) {
    activeTab.value = tab;
}

const user = computed(
    () => page.props.user
)

const notificationCount = computed(
    () => Math.min(page.props.user.notificationCount, 9),
)

</script>

<style>
.h-color {
    background-color: #063970;
}
</style>