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
                                <li>
                                    <a class="dropdown-item" href="/admin/reports/service-report">Service Reports</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/admin/reports/generate-report">Generate Reports</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item" :class="{ 'active': activeLink === 'forum' }">
                            <a class="nav-link forum text-light" href="/admin/forum"
                                @click="setActiveLink('forum')">Forum</a>
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
                                <li><a class="dropdown-item" href="/admin/problems">Problems</a></li>
                                <li><a class="dropdown-item" href="/admin/logs">Logs</a></li>
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
                                    style="font-size: small; top: 0px; right: -14px; padding: 2px 5px 2px 5px;">
                                    {{ notificationCount }}
                                </span>
                            </span>
                        </button>
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
                                    {{ page.props.user.name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-lg-end dropdown-menu-start mt-2">
                                    <li>
                                        <Link :href="route('admin.profile')" v-if="page.props.user"
                                            class="text-decoration-none dropdown-item">
                                        Profile
                                        </Link>
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
                    <div id="tab-content">
                        <div class="card mt-3" v-for="notification in notifications.slice(0, 9)" :key="notification.id">
                            <button class="close-icon btn" @click="closeNotification(notification.id)">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            <!--Ticket-->
                            <div class="card-body d-flex flex-column gap-2"
                                v-if="notification.type === 'App\\Notifications\\ResolvedTicket'">
                                <Link class="text-decoration-none text-dark"
                                    :href="route('admin.tickets.show', notification.data.ticket_number)">
                                <div class="d-flex flex-row">
                                    <div class="d-flex flex-column gap-2">
                                        <h5 class="card-title fw-bold text d-flex gap-2 align-items-center">
                                            Ticket #{{ notification.data.ticket_number }}
                                            is now
                                            <span class="badge bg-success">
                                                {{ notification.data.status }}
                                            </span>
                                        </h5>
                                        <p class="card-subtitle">
                                            Remarks: {{ notification.data.remarks }}
                                        </p>

                                        <p class="card-subtitle">
                                            Service Report No.: {{ notification.data.sr_no }}
                                        </p>
                                        <div class="card-subtitle d-flex flex-row gap-1">
                                            <span>
                                                Technician/s:
                                            </span>
                                            <br>
                                            <div>
                                                {{ notification.data.technicians }} <br>
                                            </div>
                                        </div>
                                    </div>
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
                                </Link>
                            </div>
                            <div class="card-body d-flex flex-column gap-2"
                                v-if="notification.type === 'App\\Notifications\\TicketMade'">
                                <Link class="text-decoration-none text-dark"
                                    :href="route('admin.tickets.show', notification.data.ticket_number)">
                                <div class="d-flex flex-row">
                                    <div class="d-flex flex-column gap-2">
                                        <h5 class="card-title fw-bold d-flex flex-row align-items-center gap-3">
                                            Ticket #{{ notification.data.ticket_number }} <span
                                                class="badge bg-danger">New</span>
                                        </h5>
                                        <p class="card-subtitle">
                                            Issue: {{ notification.data.description }}
                                        </p>
                                        <p class="card-subtitle">
                                            Description: {{ notification.data.description }}
                                        </p>
                                        <div class="card-subtitle d-flex flex-row gap-1">
                                            <span>
                                                Technician/s:
                                            </span>
                                            <br>
                                            <div>
                                                {{ notification.data.technicians }} <br>
                                            </div>
                                        </div>
                                    </div>
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
                                </Link>
                            </div>
                            <!-- <div class="card-body d-flex flex-column gap-2" v-if="notification.notification.type === 'App\\Notifications\\ArchivedTickets'">
                                <div class="d-flex flex-row">
                                    <div class="d-flex flex-column gap-2">
                                        <h5 class="card-title fw-bold d-flex flex-row align-items-center gap-3">
                                            {{ notification.notification.data.title }} <span
                                                class="badge bg-danger">Reminder</span>
                                        </h5>
                                        <div class="card-subtitle ">
                                            <p>{{ notification.notification.data.message }}</p>
                                            <p class="fw-bold text-uppercase">
                                                {{ formatMonthYear(notification.notification.data.date) }}
                                            </p>
                                        </div>
                                        <p class="card-subtitle text-danger fw-bold">
                                            {{ notification.notification.data.message_two }}
                                        </p>

                                        <p class="card-subtitle">Click
                                            <a href="/admin/reports/generate-report">
                                                here
                                            </a>
                                            to see the generated report!
                                        </p>
                                        <small class="card-text fst-italic text-muted">
                                            {{ formatDateTime(notification.notification.created_at) }}
                                        </small>
                                    </div>
                                </div>
                            </div> -->
                            <!--Forum-->
                            <div class="card-body" v-if="notification.type === 'App\\Notifications\\PostMade'">
                                <a :href="route('admin.forum.post', [notification.data.post_id])"
                                    class="text-decoration-none">
                                    <p class="card-title text-dark" style="width: 90%;">
                                        <strong>{{ notification.data.name }}</strong> posted in the
                                        <strong>Forum</strong>
                                    </p>
                                    <small class="card-text fst-italic text-muted">
                                        {{ formatDateTime(notification.created_at) }}
                                    </small>
                                </a>
                            </div>
                            <div class="card-body" v-if="notification.type === 'App\\Notifications\\UserTaggedPost'">
                                <a :href="route('admin.forum.post', [notification.data.post_id])"
                                    class="text-decoration-none">
                                    <p class="card-title text-dark" style="width: 90%;">
                                        <strong>{{ notification.data.post_user.name }}</strong> mentioned you in a post.
                                    </p>
                                    <small class="card-text fst-italic text-muted">
                                        {{ formatDateTime(notification.created_at) }}
                                    </small>
                                </a>
                            </div>
                            <div class="card-body" v-if="notification.type === 'App\\Notifications\\CommentMade'">
                                <a :href="route('admin.forum.post', [notification.data.post_id])"
                                    class="text-decoration-none">
                                    <p class="card-title text-dark" style="width: 90%;">
                                        <strong>{{ notification.data.name }}</strong> commented on your post, <strong>{{
                            notification.data.title }}</strong>, in the <strong>Forum</strong>
                                    </p>
                                    <small class="card-text fst-italic text-muted">
                                        {{ formatDateTime(notification.created_at) }}
                                    </small>
                                </a>
                            </div>
                            <div class="card-body" v-if="notification.type === 'App\\Notifications\\UserTaggedComment'">
                                <a :href="route('admin.forum.post', [notification.data.post_id])"
                                    class="text-decoration-none">
                                    <p class="card-title text-dark" style="width: 90%;">
                                        <strong>{{ notification.data.comment_user.name }}</strong> mentioned you in a
                                        comment.
                                    </p>
                                    <small class="card-text fst-italic text-muted">
                                        {{ formatDateTime(notification.created_at) }}
                                    </small>
                                </a>
                            </div>
                            <div class="card-body" v-if="notification.type === 'App\\Notifications\\ReplyMade'">
                                <Link :href="route('admin.forum.post', [notification.data.post_id])"
                                    class="text-decoration-none">
                                <p class="card-title w-75 text-dark">
                                    <strong>{{ notification.data.name }}</strong> replied to your comment.
                                </p>
                                <small class="card-text fst-italic text-muted">
                                    {{ formatDateTime(notification.created_at) }}
                                </small>
                                </Link>
                            </div>
                            <div class="card-body" v-if="notification.type === 'App\\Notifications\\UserTaggedReply'">
                                <a :href="route('admin.forum.post', [notification.data.post_id])"
                                    class="text-decoration-none">
                                    <p class="card-title text-dark" style="width: 90%;">
                                        <strong>{{ notification.data.comment_user.name }}</strong> mentioned you in a
                                        reply to a comment.
                                    </p>
                                    <small class="card-text fst-italic text-muted">
                                        {{ formatDateTime(notification.created_at) }}
                                    </small>
                                </a>
                            </div>
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
        setActiveLink('settings');
    } else if (currentPath.includes('forum')) {
        setActiveLink('forum');
    } else {
        setActiveLink('dashboard');
    }
}

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

const formatMonthYear = (datetime) => {
    const formattedDate = moment(datetime).format("MMMM YYYY");
    return formattedDate;
}

const page = usePage();

function showTab(tab) {
    activeTab.value = tab;
}

const notifications = ref([]);

const notificationCount = computed(
    () => Math.min(page.props.user.notificationCount, 9),
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

const closeNotification = async (notificationId) => {
    try {
        console.log('marking', notificationId)
        await axios.delete(route('admin.notifications.marked', notificationId))
        notifications.value = notifications.value.filter(notification => notification.notification.id !== notificationId);
    } catch (err) {
        console.error(err)
    }
}

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

@media (max-width: 1030px) {
    .header {
        width: 100%;
    }
}
@media (max-width: 770px) {
    .header {
        width: 100%;
    }
}

@media (max-width: 430px) {
    .header {
        width: 100%;
    }
}

@media (max-width: 380px) {
    .header {
        width: 100%;
    }

    .logo {
        visibility: hidden;
    }
}

@media (max-width: 330px) {
    .header {
        width: 100%;
    }
}
</style>
