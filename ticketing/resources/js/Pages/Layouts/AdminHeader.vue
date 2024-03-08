<template>
    <div class="header">
        <nav class="navbar navbar-expand-lg shadow-sm header-color">
            <div class="container-fluid gap-3">
                <div class="d-flex gap-2 col-6">
                    <Logo class="logo"></Logo>

                    <a class="navbar-brand text-white" href="/admin">TMDD Ticketing System</a>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item" :class="{ 'active': activeLink === 'dashboard' }">
                            <a class="nav-link dashboard text-white" aria-current="page" href="/admin"
                                @click="setActiveLink('dashboard')">Dashboard</a>
                        </li>
                        <li class="nav-item" :class="{ 'active': activeLink === 'tickets' }">
                            <a class="nav-link tickets text-light" href="/admin/tickets"
                                @click="setActiveLink('tickets')">Tickets</a>
                        </li>
                        <li class="nav-item dropdown reports" :class="{ 'active': activeLink === 'reports' }">
                            <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdownReports"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Reports
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownReports">
                                <li><a class="dropdown-item" href="/admin/reports/service-report">Service Reports</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown settings" :class="{ 'active': activeLink === 'settings' }">
                            <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Settings
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/admin/users">Users</a></li>
                                <li><a class="dropdown-item" href="/admin/department">Departments</a></li>
                                <li><a class="dropdown-item" href="/admin/office">Offices</a></li>
                                <li><a class="dropdown-item" href="/admin/services">Services</a></li>
                            </ul>
                        </li>
                    </ul>

                    <div class="d-flex gap-2 pe-3 justify-content-center align-items-center profile">
                        <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#notificationBar"
                            aria-controls="notificationBar" @click="fetchNotifications">
                            <i class="bi bi-bell text-white" style="font-size: 20px;"></i>
                            <span v-if="notificationCount" class="position-relative">
                                <span class=" position-absolute translate-middle badge rounded-pill bg-danger"
                                    id="count"
                                    style="font-size: small; top: -5px; right: -5px; padding: 2px 5px 2px 5px;">{{
                            notificationCount }}</span>
                            </span>
                        </button>
                        <div class="d-flex flex-row gap-2 justify-content-center align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white"
                                class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path fill-rule="evenodd"
                                    d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                            </svg>
                            <div class="dropdown">
                                <a class="text-decoration-none dropdown-toggle text-white" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ page.props.user.name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-lg-end dropdown-menu-start mt-2">
                                    <li>
                                        <Link :href="route('admin.change', page.props.user.id)" v-if="page.props.user"
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

        <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationBar" aria-labelledby="notificationBarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="notificationBarLabel">Notifications</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="container">
                    <nav class="nav nav-pills nav-fill navbar-light" style="background-color: #fafafa;">
                        <a class="nav-link" :class="{ 'active': activeTab === 'technician' }" aria-current="page"
                            href="#technician-content" @click.prevent="showTab('technician')">Technician<span
                                v-if="technicianCount"
                                class="position-absolute translate-middle badge rounded-pill bg-danger" id="count"
                                style="font-size: small; top: 100px; right: 185px; padding: 2px 5px 2px 5px;">{{
                            technicianCount }}</span></a>
                        <a class="nav-link" :class="{ 'active': activeTab === 'employee' }" href="#employee-content"
                            @click.prevent="showTab('employee')">Employee<span v-if="employeeCount"
                                class="position-absolute translate-middle badge rounded-pill bg-danger" id="count"
                                style="font-size: small; top: 100px; right: 15px; padding: 2px 5px 2px 5px;">{{
                            employeeCount }}</span></a>
                    </nav>
                    <div id="tab-content">
                        <div v-if="activeTab === 'technician'" class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">Notification Title</h5> <!--PLACEHOLDER-->
                                <p class="card-text">Technician is going on a vacation</p> <!--PLACEHOLDER-->
                                <small class="card-text fst-italic text-muted">{{ notificationDateTime() }}</small>
                            </div>
                        </div>
                        <div v-if="activeTab === 'employee'" class="card mt-3"
                            v-for="notification in notifications.slice(0, 9)" :key="notification.notification.id">
                            <div>
                                <div class="card-body"
                                    v-if="notification.notification.type === 'App\\Notifications\\TicketMade'">
                                    <div class="d-flex flex-row">
                                        <div class="d-flex flex-column">
                                            <h5 class="card-title fw-bold">{{ notification.notification.data.issue }}
                                            </h5>
                                            <p class="card-text">{{ notification.notification.data.description }}</p>
                                        </div>
                                    </div>
                                    <small class="card-text">{{ notification.name }} | {{ notification.department }} -
                                        {{
                            notification.office }}
                                    </small>
                                    <br>
                                    <small class="card-text fst-italic text-muted">{{
                            formatDate(notification.notification.created_at) }} /
                                        {{ formatTime(notification.notification.created_at) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import axios from 'axios';
import { computed, ref, defineProps, reactive, onMounted } from 'vue';
import moment from "moment";
import Logo from '@/Components/Logo.vue';

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
    } else if (currentPath.includes('reports')) {
        setActiveLink('reports');
    } else if (currentPath.includes('users') || currentPath.includes('department') || currentPath.includes('office')) {
        setActiveLink('settings');

    } else {
        setActiveLink('dashboard');
    }
}

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

const formatTime = (time) => {
    return moment(time, 'HH:mm:ss').format('hh:mm A');
}

const page = usePage();

function notificationDateTime() {
    const currentDateTime = new Date();
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return currentDateTime.toLocaleDateString('en-US', options).replace(/\//g, '-');
}

function showTab(tab) {
    activeTab.value = tab;
}

const notifications = ref([]);

const notificationCount = computed(
    () => Math.min(page.props.user.notificationCount, 9),
)

const employeeCount = computed(
    () => Math.min(notifications.value.length, 9),
)

const technicianCount = computed(
    () => Math.min(0, 9)
)

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('admin.notifications'))
        notifications.value = response.data.notifications
        console.log(response.data.notifications);

        await axios.post(route('admin.notifications.seen'))
        page.props.user.notificationCount = 0;
    } catch (err) {
        console.error(err)
    }
}

onMounted(() => {
    determineActiveLink(); // Set initial active link based on the current URL
});

</script>


<style scoped>
.nav-link.dashboard::after,
.nav-link.tickets::after,
.nav-link.reports::after {
    content: '';
    display: block;
    width: 0;
    height: 2px;
    background-color: white;
    transition: width 0.3s ease;
}

.nav-item.active .nav-link.dashboard::after,
.nav-item.active .nav-link.tickets::after,
.nav-item.active .nav-link.reports::after {
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
        width: 145%;
    }
}

@media (max-width: 375px) {
    .header {
        width: 157%;
    }

    .logo {
        visibility: hidden;
    }
}

@media (max-width: 325px) {
    .header {
        width: 175%;
    }
}
</style>
