<template>

    <div class="position-absolute end-0 me-3" style="z-index: 100; margin-top: 5rem;">
        <Toast
            x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
            x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown"
            v-if="showSuccessToast" :success="page.props.flash.success" :message="page.props.flash.message"
            @close="handleClose">
        </Toast>

        <Toast
            x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
            x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown"
            v-if="showErrorToast" :error="page.props.flash.error" :error_message="page.props.flash.error_message"
            @close="handleClose">
        </Toast>
    </div>
    <div>
        <Header class="sticky-top" style="z-index: 110;"></Header>

        <div class="d-flex gap-4 mt-2">
            <div class="">
                <a :href="route('observer.tickets')"
                    class="print-hidden btn btn-secondary m-2 d-flex flex-row justify-content-start align-items-center"
                    style="width: 6rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                        class="bi bi-caret-left-fill print-hidden" viewBox="0 0 16 16">
                        <path
                            d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z" />
                    </svg>
                    <span class="">Back</span>
                </a>
            </div>

            <div class="d-flex flex-grow-1 gap-2 w-100 align-items-center">
                <div class="d-flex gap-2 border px-3 rounded">
                    <p class="fw-bold text-secondary pt-3">RS - {{ rs ? rs.rs_no : 0 }} |</p>
                    <p class="fw-bold text-secondary pt-3">MS - {{ ms ? ms.ms_no : 0 }} |</p>
                    <p class="fw-bold text-secondary pt-3">RR - {{ rr ? rr.rr_no : 0 }} |</p>
                    <p class="fw-bold text-secondary pt-3">SR - {{ sr ? sr.sr_no : 0 }}</p>
                </div>
                <div class="d-flex gap-3 p-2 rounded border" style="overflow-x: auto; max-width: 58rem;">
                    <div class="d-flex align-items-center flex-nowrap" v-for="(assignedTech, index) in ticket.assigned"
                        :key="index">
                        <div class="d-flex align-items-center gap-1" v-for="tech in assignedTech.technician">
                            <div class="">
                                <img v-if="tech.user.avatar !== 'http://127.0.0.1:8000/storage'" :src="tech.user.avatar"
                                    alt="User profile picture" class="avatar rounded-circle">
                                <EmptyProfile v-else class="avatar rounded-circle"></EmptyProfile>
                            </div>
                            <small class="fw-semibold">{{ tech.user.name }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-4 justify-content-center align-items-center">
            <div class="row row-cols-sm-1 gap-4 justify-content-center">
                <div class="col-lg-8 d-flex flex-column justify-content-around gap-4 shadow rounded py-4 px-4 ticket">
                    <div class="d-flex flex-row justify-content-between gap-5 px-3">
                        <div>
                            <h7 class="text-secondary">Ticket Number</h7>
                            <h5>#{{ ticket.ticket_number }}</h5>
                        </div>
                        <div>
                            <div class="text-start d-flex flex-row gap-4">
                                <div class="d-flex flex-column" style="width: 3rem;">
                                    <h7 class="text-secondary">RR No</h7>
                                    <h5 v-if="!selectedInput || selectedInput !== 'rr'"
                                        @click="showInput(ticket.rr_no, ticket.ticket_number, 'rr')">
                                        {{ ticket.rr_no ? ticket.rr_no : 'N/A' }}
                                    </h5>
                                    <input type="text" v-if="selectedInput === 'rr'" v-model="editData[ticket.rr_no]"
                                        @blur="updateData(ticket.rr_no, ticket.ticket_number, 'rr_no', 'rr')"
                                        @keyup.enter="updateData(ticket.rr_no, ticket.ticket_number, 'rr_no', 'rr')"
                                        class="rounded border border-secondary-subtle text-center">
                                </div>
                                <div class="d-flex flex-column" style="width: 3.5rem;">
                                    <h7 class="text-secondary">MS No</h7>
                                    <h5 v-if="!selectedInput || selectedInput !== 'ms'"
                                        @click="showInput(ticket.rr_no, ticket.ticket_number, 'ms')">
                                        {{ ticket.ms_no ? ticket.ms_no : 'N/A' }}
                                    </h5>
                                    <input type="text" v-if="selectedInput === 'ms'" v-model="editData[ticket.ms_no]"
                                        @blur="updateData(ticket.ms_no, ticket.ticket_number, 'ms_no', 'ms')"
                                        @keyup.enter="updateData(ticket.ms_no, ticket.ticket_number, 'ms_no', 'ms')"
                                        class="rounded border border-secondary-subtle text-center">
                                </div>
                                <div class="d-flex flex-column" style="width: 3.5rem;">
                                    <h7 class="text-secondary">RS No</h7>
                                    <h5 v-if="!selectedInput || selectedInput !== 'rs'"
                                        @click="showInput(ticket.rs_no, ticket.ticket_number, 'rs')">
                                        {{ ticket.rs_no ? ticket.rs_no : 'N/A' }}
                                    </h5>
                                    <input type="text" v-if="selectedInput === 'rs'" v-model="editData[ticket.rs_no]"
                                        @blur="updateData(ticket.rs_no, ticket.ticket_number, 'rs_no', 'rs')"
                                        @keyup.enter="updateData(ticket.rs_no, ticket.ticket_number, 'rs_no', 'rs')"
                                        class="w-100 rounded border border-secondary-subtle text-center">
                                </div>
                                <div class="d-flex flex-column" style="width: 3.5rem;">
                                    <h7 class="text-secondary">SR No</h7>
                                    <h5 v-if="!selectedInput || selectedInput !== 'sr'"
                                        @click="showInput(ticket.sr_no, ticket.ticket_number, 'sr', ticket.status)">
                                        {{ ticket.sr_no ? ticket.sr_no : 'N/A' }}
                                    </h5>
                                    <input type="text" v-if="selectedInput === 'sr'" v-model="editData[ticket.sr_no]"
                                        @blur="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no', 'sr')"
                                        @keyup.enter="updateData(ticket.sr_no, ticket.ticket_number, 'sr_no')"
                                        class="w-100 rounded border border-secondary-subtle text-center">
                                </div>
                            </div>
                        </div>
                        <div>
                            <h7 class="text-secondary">Date Created</h7>
                            <h5>{{ formatDate(ticket.created_at) }}</h5>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between gap-5 px-3">
                        <div>
                            <h7 class="text-secondary">Complexity</h7>
                            <div v-if="ticket.status !== 'Resolved'" class="">
                                <button type="button" :class="getComplexityClass(ticket.complexity)" class="fs-5"
                                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                    {{ ticket.complexity ? ticket.complexity : 'N/A' }}
                                </button>
                            </div>
                            <div v-else-if="ticket.status == 'Resolved'">
                                <button type="button" :class="getComplexityClass(ticket.complexity)"
                                    class="text-center px-3">
                                    {{ ticket.complexity ? ticket.complexity : 'N/A' }}
                                </button>
                            </div>
                        </div>
                        <div>
                            <h7 class="text-secondary">Request Type</h7>
                            <h5>{{ !ticket.rs_no ? ticket.request_type : 'Requisition Slip' }}</h5>
                        </div>
                        <div>
                            <h7 class="text-secondary">Employee</h7>
                            <h5>{{ ticket.employee.user.name }}</h5>
                        </div>
                        <div>
                            <h7 class="text-secondary">Department and Office</h7>
                            <h5>{{ ticket.employee.department }} - {{ ticket.employee.office }}</h5>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between gap-5 px-3">
                        <div>
                            <h7 class="text-secondary">Issue</h7>
                            <h5>{{ ticket.issue }}</h5>
                        </div>
                        <div>
                            <h7 class="text-secondary">Service</h7>
                            <div v-if="ticket.status !== 'Resolved'" class="">
                                <button type="button" class="btn text-center p-0" data-bs-toggle="dropdown"
                                    aria-expanded="false" data-bs-reference="parent">
                                    <h5>
                                        {{ ticket.service ? ticket.service : 'Unassigned' }}
                                    </h5>
                                </button>
                            </div>
                            <div v-else-if="ticket.status == 'Resolved'">
                                <button type="button" class="btn text-center p-0">
                                    <h5>
                                        {{ ticket.service ? ticket.service : 'N/A' }}
                                    </h5>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-column w-25">
                            <h7 class="text-secondary">Remarks</h7>
                            <h5 v-if="!selectedInput || selectedInput !== 'remarks'"
                                @click="showInput(ticket.remarks, ticket.ticket_number, 'remarks')">
                                {{ ticket.remarks ? ticket.remarks : 'N/A' }}
                            </h5>
                            <textarea v-if="selectedInput === 'remarks'" v-model="editData[ticket.remarks]"
                                @blur="updateData(ticket.remarks, ticket.ticket_number, 'remarks', 'remarks')"
                                @keyup.enter="updateData(ticket.remarks, ticket.ticket_number, 'remarks', 'remarks')"
                                class="rounded border border-secondary-subtle text-center"></textarea>
                        </div>
                    </div>
                    <div class="d-flex flex-row justify-content-between gap-5 px-3">
                        <div class="w-75">
                            <h7 class="text-secondary">Description</h7>
                            <h5>{{ ticket.description }}</h5>
                        </div>
                        <div class="w-25">
                            <h7 class="text-secondary ">Status</h7>
                            <div class="">
                                <button type="button" :class="getButtonClass(ticket.status)" class="fs-5 px-3"
                                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-reference="parent">
                                    {{ ticket.status }}
                                </button>
                            </div>
                        </div>
                        <div class="w-25">
                            <h7 class="text-secondary ">Resolved</h7>
                            <h5>
                                {{ isNaN(new Date(formatDate(ticket.resolved_at))) ? 'Not yet done' :
                formatDate(ticket.resolved_at) }}
                            </h5>
                        </div>
                    </div>
                </div>

                <!-- Task List Section -->
                <div class="col-lg-3 shadow rounded p-4 task">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <h6>Tasks</h6>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <!-- Task Section -->
                        <div v-if="tasks.length > 0" class="task-container p-2">
                            <div v-for="task in tasks" :key="task.id"
                                class="accordion d-flex justify-content-between align-items-center mb-2">
                                <div class="accordion-item d-flex flex-column p-2" style="width: 20rem;">
                                    <div class="d-flex flex-row justify-content-between align-items-center">
                                        <div class="accordion-header d-flex flex-row justify-content-between">
                                            <div class="d-flex flex-row justify-content-start gap-3 align-items-center">
                                                <input class="form-check-input" type="checkbox"
                                                    :checked="task.is_resolved"
                                                    :disabled="!task.is_resolved" 
                                                    @change="resolveTask(task)">
                                                <label v-if="selectedInput !== task.id"
                                                    class="form-check-label overflow-control fw-medium text-truncate"
                                                    style="max-width: 10rem;" :title="task.task_name"
                                                    @click="editTaskName(task)"
                                                    :class="{ 'text-body-tertiary': task.is_resolved, 'text-decoration-line-through': task.is_resolved }">
                                                    {{ task.task_name }}
                                                </label>


                                                <input v-if="selectedInput === task.id" v-model="editData[task.id]"
                                                    @blur="updateTaskName(editData[task.id], task.id)"
                                                    @keyup.enter="updateTaskName(editData[task.id], task.id)"
                                                    class="form-control" :disabled="task.is_resolved" />
                                            </div>
                                        </div>
                                        <button class="btn" type="button" :data-bs-toggle="'collapse'"
                                            :data-bs-target="'#collapse' + task.id" aria-expanded="false"
                                            :aria-controls="'collapse' + task.id">
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>

                                    <div :id="'collapse' + task.id" class="accordion-collapse collapse"
                                        aria-labelledby="headingOne">
                                        <div class="accordion-body">
                                            <div class="name-and-date d-flex flex-row">
                                                <small class="text-muted tasks-date">{{ task.user.name }}</small>
                                            </div>
                                            <div class="name-and-date d-flex flex-row">
                                                <small class="text-muted tasks-date">{{ formatDateTime(task.created_at) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Secondary text for formatted created_at -->
                        </div>
                        <div v-else>
                            No tasks for now.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';

import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/ObserverHeader.vue";
import { usePage } from '@inertiajs/vue3';
import Alpine from 'alpinejs';
import moment from "moment";
import { ref, watchEffect } from 'vue';

// Toast Start
Alpine.start()

const page = usePage();

let showSuccessToast = ref(false);
let showErrorToast = ref(false);

watchEffect(() => {
    showSuccessToast.value = !!page.props.flash.success;
    showErrorToast.value = !!page.props.flash.error;
})

const handleClose = () => {
    page.props.flash.success = null;
    page.props.flash.error = null;
    showSuccessToast.value = false;
    showErrorToast.value = false;
}
// Toast End

const props = defineProps({
    ticket: Object,
    comments: Object,
    replies: Object,
    services: Object,
    tasks: Object,
    rr: Object,
    ms: Object,
    rs: Object,
    sr: Object,
})

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};


const formatDateTime = (date) => {
    return moment(date).format('MMM DD, YYYY HH:mm  A');
};

const getComplexityClass = (complexity) => {
    switch (complexity) {
        case 'Simple':
            return 'btn btn-warning';
        case 'Complex':
            return 'btn btn-danger';
        default:
            return 'btn btn-secondary';
    }
};

const getButtonClass = (status) => {
    switch (status.toLowerCase()) {
        case 'new':
            return 'btn btn-danger';
        case 'pending':
            return 'btn btn-warning';
        case 'ongoing':
            return 'btn btn-info';
        case 'resolved':
            return 'btn btn-success';
        default:
            return 'btn btn-secondary';
    }
};

</script>


<style>
.task-container {
    max-height: 250px;
    overflow-y: auto;
    overflow-x: hidden;
}

.tasks-date {
    font-style: italic;
}


.btn:hover {
    scale: 0.9;
}

.ticket {
    width: 68%;
}

.task {
    width: 30%;
}

.comment {
    background-color: #f2f3f4;
}

.comment:hover {
    transform: scale(1.05);
    transition: transform 0.1s ease-in-out;
    cursor: pointer;
    background-color: #c6c6c3;
}

.reply:hover {
    transform: scale(1.05);
    transition: transform 0.1s ease-in-out;
    cursor: pointer;
    background-color: #f2f3f4;
}

.btn {
    transition: all 0.2s;
}

.btn:hover {
    transform: scale(1.1);
}

.avatar {
    width: 2rem;
    height: 2rem;
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}


.fullscreen-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    max-width: 100%;
    max-height: 90%;
}

.modal-content img {
    max-width: 100%;
    max-height: 100%;
}

.name-and-date {
    white-space: nowrap;
    /* Prevent name and date from wrapping */
}

.buttons {
    flex-shrink: 0;
    /* Prevent buttons from shrinking */
}
</style>