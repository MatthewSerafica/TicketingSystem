<template>
    <div>
    <nav class="navbar navbar-expand-lg shadow-sm header-color">
        <div class="container-fluid gap-3">
            <div class="d-flex gap-2 col-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white"
                    class="bi bi-brilliance mt-2" viewBox="0 0 16 16">
                    <path
                        d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16M1 8a7 7 0 0 0 7 7 3.5 3.5 0 1 0 0-7 3.5 3.5 0 1 1 0-7 7 7 0 0 0-7 7" />
                </svg>
                <a class="navbar-brand text-white" href="/technician">TMDD Ticketing System</a>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse gap-5  " id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item" :class="{ 'active': activeLink === 'dashboard' }">
                        <a class="nav-link text-white" aria-current="page" href="/technician" @click="setActiveLink('dashboard')">Dashboard</a>
                    </li>
                    <li class="nav-item" :class="{ 'active': activeLink === 'tickets' }">
                        <a class="nav-link text-white" href="/technician/tickets" @click="setActiveLink('tickets')">Tickets</a>
                    </li>
                    <li class="nav-item" :class="{ 'active': activeLink === 'service-report' }">
                        <a class="nav-link text-white" href="/technician/service-report" @click="setActiveLink('service-report')">Service Reports</a>
                    </li>
                </ul>
      
            <div class="d-flex gap-2 pe-5 me-5 justify-content-center align-items-center">
                <button class="btn p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificationBar"
                    aria-controls="notificationBar">
                    <i class="bi bi-bell text-white me-3" style="font-size: 20px;"></i>
                    <span class="text-light bg-danger position-absolute top-0 rounded-pill badge" id="count"
                        style="font-size: small; padding: 2px 5px 2px 5px;"></span>
                </button>

                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white"
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
        </div>
    </nav>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationBar" aria-labelledby="notificationBarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="notificationBarLabel">Notifications</h5>
            <button type="button" class="btn-close text reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="container mt-4">
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Notification Title</h5>
                        <p class="card-text">Ticket# has been given to you.</p>
                        <small class="card-text fst-italic text-muted"> {{ notificationDateTime() }}</small>
                    </div>
                </div>  
            </div>    
        </div>
    </div>
</div>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref, defineProps, onMounted } from 'vue';

const props = defineProps({});
const activeLink = ref('');

const setActiveLink = (link) => {
    activeLink.value = link;
}

const determineActiveLink = () => {
    const currentPath = window.location.pathname;
    if (currentPath.includes('tickets')) {
        setActiveLink('tickets');
    } else if (currentPath.includes('service-report')) {
        setActiveLink('service-report');
    } else {
        setActiveLink('dashboard');
    }
}

const page = usePage();

onMounted(() => {
    determineActiveLink(); 
});

function notificationDateTime(){
    const currentDateTime = new Date();
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return currentDateTime.toLocaleDateString('en-US', options).replace(/\//g, '-');
}
</script>


<style scoped>


.nav-link::after {
    content: '';
    display: block;
    width: 0;
    height: 2px;
    background-color: white;
    transition: width 0.3s ease;
}

.nav-item.active .nav-link::after {
    width: 100%;
}
</style>