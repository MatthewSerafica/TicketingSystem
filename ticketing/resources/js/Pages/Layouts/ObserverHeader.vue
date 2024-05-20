<template>
    <div class="header">
        <nav class="navbar navbar-expand-lg shadow-sm header-color">
            <div class="container-fluid gap-3">
                <div class="d-flex gap-2 col-6">
                    <Logo class="logo"></Logo>
                    <a class="navbar-brand text-white" href="/observer">TMDD Ticketing System</a>
                </div>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item" :class="{ 'active': activeLink === 'dashboard' }">
                            <a class="nav-link dashboard text-white" aria-current="page" href="/observer"
                                @click="setActiveLink('dashboard')">Dashboard</a>
                        </li>
                        <li class="nav-item" :class="{ 'active': activeLink === 'tickets' }">
                            <a class="nav-link tickets text-light" href="/observer/tickets"
                                @click="setActiveLink('tickets')">Tickets</a>
                        </li>
                        <li class="nav-item dropdown reports" :class="{ 'active': activeLink === 'reports' }">
                            <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdownReports"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Reports
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                                <li>
                                    <a class="dropdown-item" href="/observer/reports/service-report">Service Reports</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/observer/reports/generate-report">Generate Reports</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item" :class="{ 'active': activeLink === 'users' }">
                            <a class="nav-link dashboard text-white" aria-current="page" href="/observer/users"
                                @click="setActiveLink('users')">Users</a>
                        </li>
                    </ul>

                    <div class="d-flex gap-2 pe-3 justify-content-center align-items-center profile">
                        <div class="d-flex flex-row gap-2 justify-content-center align-items-center">
                            <img v-if="page.props.user.avatar !== 'http://127.0.0.1:8000/storage'"
                                :src="page.props.user.avatar" alt="User profile picture"
                                class="avatar rounded-circle shadow border-3">
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white"
                                class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path fill-rule="evenodd"
                                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg>
                            <div class="dropdown">
                                <a class="text-decoration-none dropdown-toggle text-white" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ page.props.user.name }} (Observer)
                                </a>
                                <ul class="dropdown-menu dropdown-menu-lg-end dropdown-menu-start mt-2">
                                    <li>
                                        <Link :href="route('observer.profile')" v-if="page.props.user"
                                            class="text-decoration-none dropdown-item">
                                        Profile
                                        </Link>
                                        <Link :href="route('observer.change', page.props.user.id)" v-if="page.props.user"
                                            class="text-decoration-none dropdown-item">Change password
                                        </Link>
                                        <Link :href="route('logout')" method="delete" v-if="page.props.user"
                                            class="text-decoration-none dropdown-item">Logout
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</template>

<script setup>
import Logo from '@/Components/Logo.vue';
import { Link, usePage } from "@inertiajs/vue3";
import axios from 'axios';
import { computed, defineProps, onMounted, ref } from 'vue';
import moment from 'moment';


const props = defineProps({});

const activeLink = ref('');
const activeTab = ref('technician');

const setActiveLink = (link) => {
    activeLink.value = link;
}

const determineActiveLink = () => {
    const currentPath = window.location.pathname;
    if (currentPath.includes('tickets')) {
        setActiveLink('tickets');
    } else if (currentPath.includes('service-report') || currentPath.includes('generate-report')) {
        setActiveLink('reports');
    } else if (currentPath.includes('users') || currentPath.includes('department') || currentPath.includes('office') || currentPath.includes('services') || currentPath.includes('problems') || currentPath.includes('logs')) {
        setActiveLink('users');
    }  else {
        setActiveLink('dashboard');
    }
}

const page = usePage();

onMounted(() => {
    determineActiveLink();
});

</script>


<style scoped>
.avatar {
    width: 2rem;
    /* Width of the avatar */
    height: 2rem;
    /* Height of the avatar */
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}

.close-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    transition: transform 0.5s ease;
}

.close-icon i {
    font-size: 20px;
    color: #FFB6C1;

}

.close-icon:hover i {
    color: #ff0000;
}

.close-icon:hover {
    transform: scale(1.2);
}

.nav-link.dashboard::after,
.nav-link.tickets::after,
.nav-link.reports::after,
.nav-link.forum::after {
    content: '';
    display: block;
    width: 0;
    height: 2px;
    background-color: white;
    transition: width 0.3s ease;
}

.nav-item.active .nav-link.dashboard::after,
.nav-item.active .nav-link.tickets::after,
.nav-item.active .nav-link.reports::after,
.nav-item.active .nav-link.forum::after {
    width: 100%;
}

.header {
    width: 100%;
}

.nav-item.settings .nav-link::after {
    content: none;
}

.dropdown-toggle::after {
    display: none !important;
}

@media (max-width: 768px) {
    .header {
        width: 100%;
    }
}

@media (max-width: 425px) {
    .header {
        width: 100%;
    }
}

@media (max-width: 375px) {
    .header {
        width: 100%;
    }

    .logo {
        visibility: hidden;
    }
}

@media (max-width: 325px) {
    .header {
        width: 100%;
    }
}
</style>
