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
                                <div>
                                    <h7 class="text-secondary">RR No</h7>
                                    <h5>{{ ticket.rr_no ?? '0000' }}</h5>
                                </div>
                                <div>
                                    <h7 class="text-secondary">MS No</h7>
                                    <h5>{{ ticket.ms_no ?? '0000' }}</h5>
                                </div>
                                <div>
                                    <h7 class="text-secondary">RS No</h7>
                                    <h5>{{ ticket.rs_no ?? '0000' }}</h5>
                                </div>
                                <div>
                                    <h7 class="text-secondary">SR No</h7>
                                    <h5>{{ ticket.sr_no ?? '0000' }}</h5>
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
                                <ul class="dropdown-menu">
                                    <li @click="updateComplexity(ticket.ticket_number, 'Simple')"
                                        class="btn dropdown-item">Simple
                                    </li>
                                    <li @click="updateComplexity(ticket.ticket_number, 'Complex')"
                                        class="btn dropdown-item">Complex
                                    </li>
                                </ul>
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
                            <h5>{{ ticket.service }}</h5>
                        </div>
                        <div>
                            <h7 class="text-secondary">Remarks</h7>
                            <h5>{{ ticket.remarks ?? 'N/A' }}</h5>
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
                                <ul class="dropdown-menu">
                                    <li @click="updateStatus(ticket.ticket_number, 'New', ticket.status, ticket.sr_no)"
                                        class="btn dropdown-item">New
                                    </li>
                                    <li @click="updateStatus(ticket.ticket_number, 'Pending', ticket.status, ticket.sr_no)"
                                        class="btn dropdown-item">
                                        Pending
                                    </li>
                                    <li @click="updateStatus(ticket.ticket_number, 'Ongoing', ticket.status, ticket.sr_no)"
                                        class="btn dropdown-item">
                                        Ongoing
                                    </li>
                                    <li @click="updateStatus(ticket.ticket_number, 'Resolved', ticket.status, ticket.sr_no)"
                                        class="btn dropdown-item">
                                        Resolved
                                    </li>
                                </ul>
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
                <div class="col-lg-3 shadow rounded p-4 task">
                    <div>
                        <h6>Tasks</h6>
                    </div>
                    <div>

                    </div>
                </div>
            </div>

            <div class="row mt-5 gap-4 justify-content-center text-center bg-white">
                <div class="col-lg-8 d-flex flex-column justify-content-center align-items-center gap-2">
                    <button v-if="!isShowComment" class="p-2 px-4 rounded-pill form-control border cursor-text"
                        @click="showComment">Add a comment</button>
                    <div v-if="isShowComment" class="d-flex flex-column gap-2 w-100">
                        <form @submit.prevent="comment">
                            <textarea class="p-2 px-4 rounded-4 form-control mb-2" v-model="commentForm.content"
                                @input="handleContentChange(commentForm.content)"></textarea>

                            <div class="position-relative">
                                <div class="d-flex gap-2">
                                    <div class="d-flex gap-1 align-items-center"
                                        v-for="(mention, index) in commentForm.tagged_user_name">
                                        <span style="font-size: 11px;">{{ mention }}</span>
                                        <button type="button" class="btn btn-sm rounded-circle"
                                            @click="removeMention(index)"
                                            style="--bs-btn-padding-y: .07rem; --bs-btn-padding-x: .20rem; --bs-btn-font-size: .75rem;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <ul v-if="showAutocomplete"
                                    class="autocomplete-dropdown bg-white border-secondary border p-2 rounded shadow  w-50 position-absolute"
                                    style="top: -0.5rem;z-index: 100; list-style-type: none; cursor: pointer;">
                                    <li class="text-decoration-none" v-for="user in userData" :key="user.id"
                                        @click="selectUser(user)">
                                        {{ user.name }}
                                    </li>
                                </ul>
                            </div>
                            <div class="d-flex justify-content-end py-1 px-3 border-gray-subtle gap-2">
                                <Button :name="'Cancel'" :color="'secondary'" @click="closeComment"
                                    class="rounded-pill"></Button>
                                <button type="submit" class="rounded-pill btn btn-primary">Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { useForm, usePage } from '@inertiajs/vue3';
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
})

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};


const commentTextarea = ref(null);

const isShowComment = ref(false);

function closeComment() {
    if (isShowComment.value) {
        isShowComment.value = false;
        showAutocomplete.value = false;
    }
}

function showComment() {
    if (!isShowComment.value) {
        isShowComment.value = true;
    }
}


const commentForm = useForm({
    content: null,
    tagged_user: [],
    tagged_user_name: [],
})

const comment = () => commentForm.post(route('admin.ticket.comment', [props.ticket.ticket_number]), { preserveScroll: false, preserveState: false })


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
.ticket {
    width: 68%;
}

.task {
    width: 30%;
}
</style>