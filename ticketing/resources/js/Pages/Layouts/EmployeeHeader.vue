<template>
    <div>
        <nav class="navbar navbar-expand-lg shadow-sm header-color">
            <div class="container-fluid gap-5">
                <!-- Logo -->
                <div class="d-flex gap-2 col-8">
                    <Logo class="logo"></Logo>
                    <Link class="navbar-brand text-white" :href="`/employee`">TMDD Ticketing System</Link>
                </div>

                <!-- Hamburger Menu Button -->
                <button class="navbar-toggler order-lg-1" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Hamburger Menu Content -->
                <div class="collapse navbar-collapse order-lg-3" id="navbarNav">
                    <div class="d-flex gap-2 pe-5 me-5 justify-content-center align-items-center">
                        <!-- Notification Bell -->
                        <button class="btn p-2" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#notificationBar" aria-controls="notificationBar"
                            @click="fetchNotifications">
                            <i class="bi bi-bell text-white me-3" style="font-size: 20px;"></i>
                            <span v-if="notificationCount" class="position-relative">
                                <span class="position-absolute translate-middle badge rounded-pill bg-danger"
                                    style="font-size: small; top: -5px; right: -5px; padding: 2px 5px 2px 5px;">{{
                        notificationCount }}</span>
                            </span>
                        </button>

                        <!-- Username Dropdown -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white"
                            class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                            <path fill-rule="evenodd"
                                d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                        </svg>
                        <div class="dropdown-center">
                            <a class="text-decoration-none dropdown-toggle text-white" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ page.props.user.name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <Link :href="route('employee.profile', page.props.user.id)" v-if="page.props.user"
                                        class="text-decoration-none dropdown-item">
                                    Profile
                                    </Link>
                                    <Link :href="route('employee.change', page.props.user.id)" v-if="page.props.user"
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
        </nav>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationBar" aria-labelledby="notificationBarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="notificationBarLabel">Notifications</h5>
                <button type="button" class="btn-close text reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="container mt-4">
                    <div class="card mt-3" v-for="notification in notifications.slice(0, 9)" :key="notification.id">
                        <div class="card-body" v-if="notification.type === 'App\\Notifications\\UpdateTicketStatus'">
                            <h5 class="card-title">Ticket Update</h5>
                            <p class="card-text">Your Ticket #{{ notification.data.ticket_number }} for {{
                        notification.data.service }} with the {{ notification.data.issue }} is now <span
                                    class="p-2" :class="handleBadge(notification.data.status)">{{
                        notification.data.status }}</span>.</p>
                            <small class="card-text fst-italic text-muted"> {{ notificationDateTime() }}</small>
                        </div>
                        <div class="card-body" v-if="notification.type === 'App\\Notifications\\TicketMade'">
                            <h5 class="card-title">Ticket Made</h5>
                            <div class="d-flex flex-row">
                                <div class="d-flex flex-column">
                                    <h5 class="card-title fw-bold">{{ notification.data.issue }}
                                    </h5>
                                    <p class="card-text">{{ notification.data.description }}</p>
                                </div>
                            </div>
                            <small class="card-text fst-italic text-muted">{{
                        formatDate(notification.created_at) }} /
                                {{ formatTime(notification.created_at) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Logo from '@/Components/Logo.vue';
import { Link, usePage } from "@inertiajs/vue3";
import axios from "axios";
import moment from "moment";
import { computed, ref } from "vue";

const page = usePage();

function notificationDateTime() {
    const currentDateTime = new Date();
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    return currentDateTime.toLocaleDateString('en-US', options).replace(/\//g, '-');
}

const notifications = ref([]);

const notificationCount = computed(
    () => Math.min(page.props.user.notificationCount, 9)
)

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('employee.notifications'))
        notifications.value = response.data.notifications

        await axios.post(route('employee.notifications.seen'))
        page.props.user.notificationCount = 0;
    } catch (err) {
        console.error(err)
    }
}

const handleBadge = (status) => {
    switch (status.toLowerCase()) {
        case 'new':
            return 'badge text-bg-danger';
        case 'pending':
            return 'badge text-bg-warning';
        case 'ongoing':
            return 'badge text-bg-primary';
        case 'resolved':
            return 'badge text-bg-success';
        default:
            return 'badge text-bg-secondary';
    }
}

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

const formatTime = (time) => {
    return moment(time, 'HH:mm:ss').format('hh:mm A');
}

</script>

<style>
.header-color {
    background-color: #063970;
}
</style>
