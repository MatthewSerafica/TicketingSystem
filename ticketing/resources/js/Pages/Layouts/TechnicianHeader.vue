<template>
    <div>
        <nav class="navbar navbar-expand-lg shadow-sm header-color">
            <div class="container-fluid gap-3">
                <div class="d-flex gap-2 col-6">
                    <Logo class="logo"></Logo>
                    <a class="navbar-brand text-white" href="/technician">TMDD Ticketing System</a>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse gap-5  " id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item" :class="{ 'active': activeLink === 'dashboard' }">
                            <a class="nav-link text-white" aria-current="page" href="/technician"
                                @click="setActiveLink('dashboard')">Dashboard</a>
                        </li>
                        <li class="nav-item" :class="{ 'active': activeLink === 'tickets' }">
                            <a class="nav-link text-white" href="/technician/tickets"
                                @click="setActiveLink('tickets')">Tickets</a>
                        </li>
                        <li class="nav-item" :class="{ 'active': activeLink === 'service-report' }">
                            <a class="nav-link text-white" href="/technician/service-report"
                                @click="setActiveLink('service-report')">Service Report</a>
                        </li>
                        <li class="nav-item" :class="{ 'active': activeLink === 'forum' }">
                            <a class="nav-link text-white" href="/technician/forum"
                                @click="setActiveLink('forum')">Forum</a>
                        </li>
                    </ul>

                    <div class="d-flex gap-2 pe-5 me-5 justify-content-center align-items-center">
                        <button class="btn p-2" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#notificationBar" aria-controls="notificationBar"
                            @click="fetchNotifications">
                            <i class="bi bi-bell text-white me-3" style="font-size: 20px;"></i>
                            <span v-if="notificationCount" class="position-relative">
                                <span class="position-absolute translate-middle badge rounded-pill bg-danger"
                                    style="font-size: small; top: -5px; right: -5px; padding: 2px 5px 2px 5px;">
                                    {{ notificationCount }}
                                </span>
                            </span>
                        </button>

                        <img v-if="page.props.user.avatar !== 'http://127.0.0.1:8000/storage'" :src="page.props.user.avatar" alt="User profile picture"
                            class="avatar rounded-circle shadow border-3">
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white"
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
                                    <Link :href="route('technician.profile')" v-if="page.props.user"
                                        class="text-decoration-none dropdown-item">
                                    Profile
                                    </Link>
                                    <Link :href="route('technician.change', page.props.user.id)" v-if="page.props.user"
                                        class="text-decoration-none dropdown-item">
                                    Change password
                                    </Link>
                                    <Link :href="route('logout')" method="delete" v-if="page.props.user"
                                        class="text-decoration-none dropdown-item">
                                    Logout
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
                        <button class="close-icon btn" @click="closeNotification(notification.id)">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        <div class="card-body d-flex flex-column gap-2"
                            v-if="notification.type === 'App\\Notifications\\UpdateTicketTechnician'">
                            <div class="d-flex flex-column gap-2">
                                <h5 class="card-title">
                                    You are assigned to <br> Ticket #{{ notification.data.ticket_number }}
                                </h5>
                                <p class="card-subtitle">Issue: {{ notification.data.issue }}</p>
                                <p class="card-subtitle">Description: {{ notification.data.description }}
                                </p>
                                <p class="card-subtitle">Service Type: {{ notification.data.service }}</p>
                            </div>
                            <div>
                                <small class="card-text">
                                    {{ notification.data.name }} | {{ notification.data.department }} -
                                    {{ notification.data.office }}
                                </small>
                                <br>
                                <small class="card-text fst-italic text-muted">
                                    {{ formatDateTime(notification.created_at) }}
                                </small>
                            </div>
                        </div>
                        <div class="card-body"
                            v-if="notification.type === 'App\\Notifications\\UpdateTechnicianReplace'">
                            <h5 class="card-title">
                                You have been replaced for <br> Ticket #{{ notification.data.ticket_number }}
                            </h5>
                            <p class="card-subtitle">Service: {{ notification.data.service }}</p>
                            <div>
                                <small class="card-text">
                                    {{ notification.data.name }} | {{ notification.data.department }} -
                                    {{ notification.data.office }}
                                </small>
                                <br>
                                <small class="card-text fst-italic text-muted">
                                    {{ formatDateTime(notification.created_at) }}
                                </small>
                            </div>
                        </div>
                        <div class="card-body" v-if="notification.type === 'App\\Notifications\\UpdateTicketStatus'">
                            <h5 class="card-title">
                                Ticket #{{ notification.data.ticket_number }} Status Update
                            </h5>
                            <p class="card-text">
                                Ticket #{{ notification.data.ticket_number }} is now
                                <span class="p-2" :class="handleBadge(notification.data.status)">
                                    {{ notification.data.status }}
                                </span>
                            </p>
                            <small class="card-text fst-italic text-muted">
                                {{ formatDateTime(notification.created_at) }}
                            </small>
                        </div>
                        <div class="card-body" v-if="notification.type === 'App\\Notifications\\PostMade'">
                            <p class="card-title" style="width: 90%;">
                                <strong>{{ notification.data.name }}</strong> posted <strong>{{ notification.data.title
                                    }}</strong> in the <strong>Forum</strong>
                            </p>
                            <small class="card-text fst-italic text-muted">
                                {{ formatDateTime(notification.created_at) }}
                            </small>
                        </div>
                        <div class="card-body" v-if="notification.type === 'App\\Notifications\\CommentMade'">
                            <p class="card-title" style="width: 90%;">
                                <strong>{{ notification.data.name }}</strong> commented on your post, <strong>{{
                            notification.data.title }}</strong>, in the <strong>Forum</strong>
                            </p>
                            <small class="card-text fst-italic text-muted">
                                {{ formatDateTime(notification.created_at) }}
                            </small>
                        </div>
                        <div class="card-body" v-if="notification.type === 'App\\Notifications\\ReplyMade'">
                            <p class="card-title w-75">
                                <strong>{{ notification.data.name }}</strong> replied to your comment on the post,
                                <strong>{{ notification.data.ttile }}</strong>, in the
                                <strong>Forum</strong>
                            </p>
                            <small class="card-text fst-italic text-muted">
                                {{ formatDateTime(notification.created_at) }}
                            </small>
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
    } else if (currentPath.includes('forum')) {
        setActiveLink('forum');
    } else {
        setActiveLink('dashboard');
    }
}

const page = usePage();

onMounted(() => {
    determineActiveLink();
});

const formatDate = (date) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = new Date(date).toLocaleDateString(undefined, options);
    return formattedDate;
}

const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const period = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = hours % 12 || 12;
    const formattedTime = `${formattedHours}:${minutes} ${period}`;
    return formattedTime;
}
const formatDateTime = (datetime) => {
    const date = new Date(datetime);
    const formattedDate = formatDate(date);
    const formattedTime = formatTime(date.toTimeString().split(' ')[0]);
    return `${formattedDate} | ${formattedTime}`;
}


const notificationCount = computed(
    () => Math.min(page.props.user.notificationCount, 9)
)
const notifications = ref([]);

const closeNotification = async (notificationId) => {
    try {
        console.log('marking', notificationId)
        await axios.delete(route('technician.notifications.marked', notificationId))
        notifications.value = notifications.value.filter(notification => notification.id !== notificationId);
    } catch (err) {
        console.error(err)
    }
}

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('technician.notifications'))
        notifications.value = response.data.notifications
        console.log(response.data.notifications);
        await axios.post(route('technician.notifications.seen'))
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

</script>


<style scoped>
.close-icon {
    position: absolute;
    top: 8px;
    right: 0px;
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

.avatar {
    width: 3rem;
    /* Width of the avatar */
    height: 3rem;
    /* Height of the avatar */
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}
</style>