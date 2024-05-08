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
                <a :href="route('admin.tickets')"
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
                                <ul class="dropdown-menu">
                                    <li @click="updateComplexity(ticket.ticket_number, 'Simple')"
                                        class="btn dropdown-item">Simple
                                    </li>
                                    <li @click="updateComplexity(ticket.ticket_number, 'Complex')"
                                        class="btn dropdown-item">Complex
                                    </li>
                                </ul>
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
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-item disabled">Select a service</li>
                                    <li v-for="service in services" class="btn dropdown-item"
                                        @click="updateService(ticket.ticket_number, service.service)">
                                        {{ service.service }}
                                    </li>
                                </ul>
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

                <!-- Task List Section -->
                <div class="col-lg-3 shadow rounded p-4 task">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <h6>Tasks</h6>
                        <!-- Button to toggle the input form -->
                        <button v-if="!showTaskInput" type="button" class="btn btn-secondary" @click="toggleTaskInput">
                            Add Task
                        </button>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <form v-if="showTaskInput" @submit.prevent="addTask">
                                <input type="text" v-model="taskForm.task_name" placeholder="Enter task"
                                    class="form-control mb-2">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-danger" @click="toggleTaskInput">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Task Section -->
                        <div v-if="tasks.length > 0" class="task-container p-2">
                            <div v-for="task in tasks" :key="task.id"
                                class="accordion d-flex justify-content-between align-items-center mb-2">
                                <div class="accordion-item d-flex flex-column p-2" style="width: 20rem;">
                                    <div class="d-flex flex-row justify-content-between align-items-center">
                                        <div class="accordion-header d-flex flex-row justify-content-between">
                                            <div class="d-flex flex-row justify-content-start gap-3 align-items-center">
                                                <input class="form-check-input" type="checkbox"
                                                    :checked="task.is_resolved" @change="resolveTask(task)">
                                                <label v-if="selectedInput !== task.id"
                                                    class="form-check-label overflow-control fw-medium text-truncate"  style="max-width: 10rem;"
                                                    :title="task.task_name"
                                                    @click="editTaskName(task)"
                                                    :class="{ 'text-body-tertiary': task.is_resolved, 'text-decoration-line-through': task.is_resolved }">
                                                    {{ task.task_name }}
                                                </label>

                                                <input v-if="selectedInput === task.id" v-model="editData[task.id]"
                                                    @blur="updateTaskName(editData[task.id], task.id)"
                                                    @keyup.enter="updateTaskName(editData[task.id], task.id)"
                                                    class="form-control" />
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
                                            <div class="name-and-date d-flex flex-row align-items-center gap-4">
                                                <small class="text-muted tasks-date">{{ task.user.name }}</small>
                                                <small class="text-muted tasks-date  ms-auto">
                                                    {{ formatDate(task.created_at) }}
                                                </small>
                                                <button class="btn btn-danger btn-sm" @click="deleteTask(task)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Secondary text for formatted created_at -->
                        </div>
                        <div v-else>
                            No tasks available.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comment Section -->
            <div class="row mt-5 gap-4 justify-content-center text-center bg-white mb-3">
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

            <div v-if="comments.length" class="row justify-content-center">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <div class="pb-2 d-flex flex-column justify-content-center w-50 commentt"
                        v-for="comment in comments" v-bind:key="comment.id">
                        <!-- Comment User Detail -->
                        <div class="post rounded px-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="d-flex align-items-center gap-2" style="margin-left: -0.5rem;">
                                        <div class="">
                                            <img v-if="comment.user.avatar !== 'http://127.0.0.1:8000/storage'"
                                                :src="comment.user.avatar" alt="User profile picture"
                                                class="avatar rounded-circle">
                                            <EmptyProfile v-else class="avatar rounded-circle">
                                            </EmptyProfile>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-3">
                                            <p class="fw-semibold text-dark"><small>{{ comment.user.name }}</small>
                                            </p>
                                            <p class="text-secondary">
                                                •
                                            </p>
                                            <p class="text-secondary"><small>{{ comment.time_since_posted }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comment Content -->
                        <div class="comment-line-connector-container">
                            <div class="comment-line-connector"></div>
                            <div class="comment-line-connector-sideways"></div>
                            <!-- Comment Content -->
                            <div>
                                <!-- Tagged Users -->
                                <div class="text-dark fw-light fst-italic" v-if="comment.tagged.length">
                                    <p><small>Tagged:
                                            <span v-for="tag in comment.tagged" class="">
                                                {{ tag.user.name }} /
                                            </span>
                                        </small>
                                    </p>
                                </div>

                                <!-- Content -->
                                <p v-if="!isShowEdit || selectedComment.id !== comment.id"
                                    class="text-secondary-emphasis">
                                    {{ comment.content }}
                                </p>

                                <!-- Edit Form -->
                                <div v-if="isShowEdit && selectedComment.id === comment.id" class="">
                                    <form @submit.prevent="edit(comment.id)">
                                        <textarea class="p-2 px-4 rounded-4 form-control" v-model="editComment.content"
                                            @input="handleContentChangeEdit(editComment.content)"></textarea>
                                        <div class="position-relative">
                                            <div class="d-flex gap-2">
                                                <div class="d-flex gap-1 align-items-center"
                                                    v-for="(mention, index) in editComment.tagged_user_name">
                                                    <span style="font-size: 11px;">{{ mention }}</span>
                                                    <button type="button" class="btn btn-sm rounded-circle"
                                                        @click="removeMentionEdit(index)"
                                                        style="--bs-btn-padding-y: .07rem; --bs-btn-padding-x: .20rem; --bs-btn-font-size: .75rem;">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <ul v-if="showAutocomplete"
                                                class="autocomplete-dropdown bg-white border-secondary border p-2 rounded shadow  w-50 position-absolute"
                                                style="top: -0.5rem;z-index: 100; list-style-type: none; cursor: pointer;">
                                                <li class="text-decoration-none" v-for="user in userData" :key="user.id"
                                                    @click="selectUserEdit(user)">
                                                    {{ user.name }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="d-flex justify-content-end py-1 px-3 gap-2">
                                            <Button :name="'Cancel'" :color="'secondary'" @click="closeEdit"
                                                class="rounded-pill" style="font-size: 12px;"></Button>
                                            <button type="submit" class="rounded-pill btn btn-primary"
                                                style="font-size: 12px;">Submit</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- CTA -->
                                <div class="d-flex justify-content-start align-items-center text-dark"
                                    style="margin-left: -2rem;">
                                    <button @click="toggleRepliesVisibility(comment.id)" class="btn">
                                        <i
                                            :class="repliesVisibility[comment.id] ? 'bi bi-dash-circle' : 'bi bi-plus-circle'">
                                        </i>
                                    </button>
                                    <div class="d-flex align-items-center justify-content-center gap-2 rounded-pill p-2 reply"
                                        @click="showReply(comment.id)" style="width: 5rem;">
                                        <i class="bi bi-chat-left-dots"></i>
                                        <span style="font-size: 12px;" class="fw-semibold">Reply</span>
                                    </div>
                                    <div v-if="page.props.user.id === comment.user.id"
                                        class="mt-1 ellipsis rounded-circle dropdown">
                                        <button data-bs-toggle="dropdown" aria-expanded="false"
                                            class="btn rounded-circle">
                                            <i class="bi bi-three-dots-vertical text-dark"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <div class="dropdown-item" style="cursor: pointer;"
                                                    @click="showEdit(comment)">
                                                    Edit
                                                </div>
                                            </li>
                                            <li>
                                                <div class="dropdown-item" style="cursor: pointer;"
                                                    @click="showDelete(comment)">
                                                    Delete
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Reply Form -->
                                <div v-if="isShowReply && selectedComment && selectedComment === comment.id"
                                    class="d-flex flex-column gap-2 border rounded-4 p-2">
                                    <form @submit.prevent="reply(comment.id)">
                                        <textarea ref="commentTextarea"
                                            class="p-2 px-4 rounded-4 form-control border-0 mb-2"
                                            v-model="commentForm.content"
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
                                        <div class="d-flex justify-content-end py-1 px-3 gap-2">
                                            <Button :name="'Cancel'" :color="'secondary'" @click="closeReply"
                                                class="rounded-pill" style="font-size: 12px;"></Button>
                                            <button type="submit" class="rounded-pill btn btn-primary"
                                                style="font-size: 12px;">Comment</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reply Section -->
                        <div v-if="repliesVisibility[comment.id]" class="reply-container">
                            <div v-for="reply in replies" v-bind:key="reply.id" class="mx-5 replyy">
                                <div v-if="reply.parent_comment_id === comment.id" class="post rounded">
                                    <!-- Reply User Details -->
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                <div class="">
                                                    <img v-if="reply.user.avatar !== 'http://127.0.0.1:8000/storage'"
                                                        :src="reply.user.avatar" alt="User profile picture"
                                                        class="avatar rounded-circle">
                                                    <EmptyProfile v-else class="avatar rounded-circle">
                                                    </EmptyProfile>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mt-3">
                                                    <p class="fw-semibold text-dark"><small>{{ reply.user.name
                                                            }}</small>
                                                    </p>
                                                    <p class="text-secondary">
                                                        •
                                                    </p>
                                                    <p class="text-secondary"><small>{{ reply.time_since_posted
                                                            }}</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Reply Content -->
                                    <div class="line-connector-container">
                                        <div class="line-connector"></div>
                                        <div class="line-connector-sideways"></div>
                                        <div>
                                            <!-- Tagged Users -->
                                            <div class="text-dark fw-light fst-italic" v-if="reply.tagged.length">
                                                <p><small>Tagged:
                                                        <span v-for="tag in reply.tagged" class="">
                                                            {{ tag.user.name }} /
                                                        </span>
                                                    </small>
                                                </p>
                                            </div>

                                            <!-- Content -->
                                            <p v-if="!isShowEdit || selectedComment.id !== reply.id"
                                                class="text-secondary-emphasis"> {{ reply.content }}
                                            </p>

                                            <!-- Reply Edit Form -->
                                            <div v-if="isShowEdit && selectedComment.id === reply.id" class="">
                                                <form @submit.prevent="edit(reply.id)">
                                                    <textarea class="p-2 px-4 rounded-4 form-control"
                                                        v-model="editComment.content"
                                                        @input="handleContentChangeEdit(editComment.content)"></textarea>
                                                    <div class="position-relative">
                                                        <div class="d-flex gap-2">
                                                            <div class="d-flex gap-1 align-items-center"
                                                                v-for="(mention, index) in editComment.tagged_user_name">
                                                                <span style="font-size: 11px;">{{ mention }}</span>
                                                                <button type="button" class="btn btn-sm rounded-circle"
                                                                    @click="removeMentionEdit(index)"
                                                                    style="--bs-btn-padding-y: .07rem; --bs-btn-padding-x: .20rem; --bs-btn-font-size: .75rem;">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <ul v-if="showAutocomplete"
                                                            class="autocomplete-dropdown bg-white border-secondary border p-2 rounded shadow  w-50 position-absolute"
                                                            style="top: -0.5rem;z-index: 100; list-style-type: none; cursor: pointer;">
                                                            <li class="text-decoration-none" v-for="user in userData"
                                                                :key="user.id" @click="selectUserEdit(user)">
                                                                {{ user.name }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="d-flex justify-content-end py-1 px-3 gap-2">
                                                        <Button :name="'Cancel'" :color="'secondary'" @click="closeEdit"
                                                            class="rounded-pill" style="font-size: 12px;"></Button>
                                                        <button type="submit" class="rounded-pill btn btn-primary"
                                                            style="font-size: 12px;">Submit</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- CTA -->
                                            <div class="d-flex justify-content-start align-items-center text-dark"
                                                style="margin-left: -2rem;">
                                                <button @click="toggleRepliesVisibility(reply.id)" class="btn">
                                                    <i
                                                        :class="repliesVisibility[reply.id] ? 'bi bi-dash-circle' : 'bi bi-plus-circle'">
                                                    </i>
                                                </button>
                                                <div class="d-flex align-items-center justify-content-center gap-2 rounded-pill p-2 reply"
                                                    @click="showReply(reply.id)" style="width: 5rem;">
                                                    <i class="bi bi-chat-left-dots"></i>
                                                    <span style="font-size: 12px;" class="fw-semibold">Reply</span>
                                                </div>
                                                <div v-if="page.props.user.id === reply.user.id"
                                                    class="mt-1 ellipsis rounded-circle dropdown">
                                                    <button data-bs-toggle="dropdown" aria-expanded="false"
                                                        class="btn rounded-circle">
                                                        <i class="bi bi-three-dots-vertical text-dark"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <div class="dropdown-item" style="cursor: pointer;"
                                                                @click="showEdit(reply)">
                                                                Edit
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="dropdown-item" style="cursor: pointer;"
                                                                @click="showDelete(reply)">
                                                                Delete
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- More Reply Form -->
                                            <div v-if="isShowReply && selectedComment && selectedComment === reply.id"
                                                class="d-flex flex-column gap-2 border rounded-4 p-2">
                                                <form @submit.prevent="replyMore(reply.id)">
                                                    <textarea class="p-2 px-4 rounded-4 form-control border-0"
                                                        v-model="commentForm.content"
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
                                                            <li class="text-decoration-none" v-for="user in userData"
                                                                :key="user.id" @click="selectUser(user)">
                                                                {{ user.name }}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="d-flex justify-content-end py-1 px-3 gap-2">
                                                        <Button :name="'Cancel'" :color="'secondary'"
                                                            @click="closeReply" class="rounded-pill"
                                                            style="font-size: 12px;"></Button>
                                                        <button type="submit" class="rounded-pill btn btn-primary"
                                                            style="font-size: 12px;">Comment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- More Replies Section -->
                                        <div v-if="repliesVisibility[reply.id]" class="replies-container">
                                            <div v-for="more in replies" v-bind:key="more.id" class="mx-5">
                                                <div v-if="more.parent_comment_id === reply.id" class="post rounded">
                                                    <!-- User Detail -->
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div
                                                            class="d-flex align-items-center justify-content-center gap-2">
                                                            <div class="mt-1 d-flex align-items-center gap-2">
                                                                <div class="">
                                                                    <img v-if="more.user.avatar !== 'http://127.0.0.1:8000/storage'"
                                                                        :src="more.user.avatar"
                                                                        alt="User profile picture"
                                                                        class="avatar rounded-circle">
                                                                    <EmptyProfile v-else class="avatar rounded-circle">
                                                                    </EmptyProfile>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                                    <p class="fw-semibold text-dark">
                                                                        <small>
                                                                            {{ more.user.name }}
                                                                        </small>
                                                                    </p>
                                                                    <p class="text-secondary">
                                                                        •
                                                                    </p>
                                                                    <p class="text-secondary">
                                                                        <small>
                                                                            {{ more.time_since_posted }}
                                                                        </small>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div v-if="page.props.user.id === more.user.id"
                                                                class="ellipsis rounded-circle dropdown">
                                                                <button data-bs-toggle="dropdown" aria-expanded="false"
                                                                    class="btn rounded-circle">
                                                                    <i class="bi bi-three-dots-vertical text-dark"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <div class="dropdown-item"
                                                                            @click="showEdit(more)">
                                                                            Edit
                                                                        </div>
                                                                    </li>
                                                                    <li>
                                                                        <div class="dropdown-item"
                                                                            @click="showDelete(more)">
                                                                            Delete
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Content -->
                                                    <div class="">
                                                        <!-- Tagged User -->
                                                        <div class="text-dark fw-light fst-italic"
                                                            v-if="more.tagged.length">
                                                            <p><small>Tagged:
                                                                    <span v-for="tag in more.tagged" class="">
                                                                        {{ tag.user.name }} /
                                                                    </span>
                                                                </small>
                                                            </p>
                                                        </div>

                                                        <!-- Content -->
                                                        <p v-if="!isShowEdit || selectedComment.id !== more.id"
                                                            class="text-secondary-emphasis"> {{ more.content }}
                                                        </p>

                                                        <!-- Edit Form -->
                                                        <div v-if="isShowEdit && selectedComment.id === more.id"
                                                            class="">
                                                            <form @submit.prevent="edit(more.id)">
                                                                <textarea class="p-2 px-4 rounded-4 form-control"
                                                                    v-model="editComment.content"
                                                                    @input="handleContentChangeEdit(editComment.content)"></textarea>
                                                                <div class="position-relative">
                                                                    <div class="d-flex gap-2">
                                                                        <div class="d-flex gap-1 align-items-center"
                                                                            v-for="(mention, index) in editComment.tagged_user_name">
                                                                            <span style="font-size: 11px;">
                                                                                {{ mention }}
                                                                            </span>
                                                                            <button type="button"
                                                                                class="btn btn-sm rounded-circle"
                                                                                @click="removeMentionEdit(index)"
                                                                                style="--bs-btn-padding-y: .07rem; --bs-btn-padding-x: .20rem; --bs-btn-font-size: .75rem;">
                                                                                <i class="bi bi-x"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <ul v-if="showAutocomplete"
                                                                        class="autocomplete-dropdown bg-white border-secondary border p-2 rounded shadow  w-50 position-absolute"
                                                                        style="top: -0.5rem;z-index: 100; list-style-type: none; cursor: pointer;">
                                                                        <li class="text-decoration-none"
                                                                            v-for="user in userData" :key="user.id"
                                                                            @click="selectUserEdit(user)">
                                                                            {{ user.name }}
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="d-flex justify-content-end py-1 px-3 gap-2">
                                                                    <Button :name="'Cancel'" :color="'secondary'"
                                                                        @click="closeEdit" class="rounded-pill"
                                                                        style="font-size: 12px;"></Button>
                                                                    <button type="submit"
                                                                        class="rounded-pill btn btn-primary"
                                                                        style="font-size: 12px;">Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <EmptyCard v-else :title="'No Comments yet...'" class="mt-2" style="height:20rem;" />
        </div>
        <Delete v-if="isShowDelete" :commentTicket="selectedComment" :type="page.props.user.user_type"
            @closeDelete="closeDelete" />
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Delete from "@/Components/DeleteModal.vue";
import EmptyCard from '@/Components/EmptyState/Comments.vue';
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { useForm, usePage } from '@inertiajs/vue3';
import Alpine from 'alpinejs';
import moment from "moment";
import { reactive, ref, watchEffect } from 'vue';

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

// Comment Start
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


let selectedComment = ref(null);
const isShowReply = ref(false);

function closeReply() {
    if (isShowReply.value) {
        selectedComment.value = null;
        isShowReply.value = false;
        showAutocomplete.value = false;
    }
}

function showReply(comment) {
    if (!isShowReply.value) {
        selectedComment.value = comment
        isShowReply.value = true;
    }
}

const repliesVisibility = ref({});

props.comments.forEach(comment => {
    repliesVisibility.value[comment.id] = true;
});

props.replies.forEach(reply => {
    repliesVisibility.value[reply.id] = true;
});

function toggleRepliesVisibility(commentId) {
    repliesVisibility.value[commentId] = !repliesVisibility.value[commentId];
}

const commentForm = useForm({
    content: null,
    tagged_user: [],
    tagged_user_name: [],
})

const comment = () => commentForm.post(route('admin.ticket.comment', [props.ticket.ticket_number]), { preserveScroll: false, preserveState: false })

const reply = (comment) => {
    selectedComment.value = comment;
    commentForm.post(route('admin.ticket.reply', [props.ticket.ticket_number, selectedComment.value]), { preserveScroll: false, preserveState: false })
}

const replyMore = (comment) => {
    selectedComment.value = comment;
    commentForm.post(route('admin.ticket.reply', [props.ticket.ticket_number, selectedComment.value]), { preserveScroll: false, preserveState: false })
}

let isShowEdit = ref(false);

function closeEdit() {
    if (isShowEdit.value) {
        selectedComment.value = null;
        isShowEdit.value = false;
        showAutocomplete.value = false;
    }
}

function showEdit(comment) {
    if (!isShowEdit.value) {
        selectedComment.value = comment;
        editComment.content = comment.content;
        editComment.tagged_user = [];
        editComment.tagged_user_name = [];

        comment.tagged.forEach(tag => {
            editComment.tagged_user.push(tag.user.id);
            editComment.tagged_user_name.push(tag.user.name);
        });
        isShowEdit.value = true;
    }
}


const editComment = useForm({
    content: null,
    tagged_user: [],
    tagged_user_name: [],
})

const edit = (comment) => editComment.put(route('admin.ticket.edit.comment', [comment]), { preserveScroll: false, preserveState: false });

const isShowDelete = ref(false);

function closeDelete() {
    if (isShowDelete.value) {
        selectedComment.value = null
        isShowDelete.value = false;
    }
}

function showDelete(post) {
    if (!isShowDelete.value) {
        selectedComment.value = post;
        isShowDelete.value = true;
    }
}

// Comment End

// Ticket Update Start
let selectedInput = ref(null);
let editData = reactive({});

const showInput = (data, id, type, status) => {
    if (status != 'Resolved') {
        console.log(data);
        selectedInput.value = type;
        selectedRow.value = id;
        editData[data] = data ? data : '';
        console.log(selectedInput.value, editData[data], selectedRow.value);
    }
}

const updateData = async (data, id, updateField, type) => {
    console.log(editData[data])
    if (selectedInput.value !== type) {
        return;
    }

    if (!validateNumericInput(editData[data], updateField)) {
        return;
    }

    const formData = {
        [updateField]: editData[data],
        ticket_number: id,
    };

    console.log('start');
    try {
        // Assuming useForm and form.put return promises
        const form = useForm(formData);
        await form.put(route('admin.tickets.update', { ticket_id: id, field: updateField }));
        console.log('finished updating data');
    } catch (error) {
        console.error('Error updating data:', error);
    }

    selectedInput.value = null;
    editData[data] = '';
};

const updateComplexity = (ticket_id, complexity) => {
    const form = useForm({
        ticket_id: ticket_id,
        complexity: complexity,
    });

    form.put(route('admin.tickets.update.complexity', { ticket_id: ticket_id }));
}

const updateService = (ticket_id, service) => {
    const form = useForm({
        service: service,
    });

    form.put(route('admin.tickets.update.service', { ticket_id: ticket_id }));
}


const validateNumericInput = (inputValue, propName) => {
    if (propName === 'remarks') {
        return true;
    }
    const isValid = inputValue === '' || /^\d+$/.test(inputValue);
    if (!isValid && inputValue !== '') {
        page.props.flash.error = `Invalid ${propName} number`;
        page.props.flash.message = `Please input numeric values only`;
        return false;
    }
    return true;
};

const updateStatus = (ticket_id, status, old_status, srNo) => {
    if (status === 'Resolved') {
        if (!srNo) {
            page.props.flash.error = 'Status Update Error'
            page.props.flash.error_message = 'Please enter a Service Request Number!'
            return;
        }
    }

    const form = useForm({
        ticket_id: ticket_id,
        status: status,
        old_status: old_status,
    });

    form.put(route('admin.tickets.update.status', { ticket_id: ticket_id }));
}

const taskForm = useForm({
    ticket_number: props.ticket.ticket_number,
    user_id: page.props.user.id,
    task_name: null,
    is_resolved: null,
})

const showTaskInput = ref(false);
const newTask = ref('');

function toggleTaskInput() {
    showTaskInput.value = !showTaskInput.value;
    newTask.value = '';
}
const addTask = () => taskForm.post(route('admin.tickets.task'), { preserveScroll: false, preserveState: false })

console.log()
const updatedTaskForm = useForm({
    ticket_number: props.ticket.ticket_number,
    task_name: null,
    is_resolved: null,
})

const updateTask = (task, id) => {
    updatedTaskForm.task_name = task;
    updatedTaskForm.put(route('admin.tickets.task.update', id), { preserveScroll: false, preserveState: false })
}

const resolveTask = (task) => {
    task.is_resolved = !task.is_resolved;
    updatedTaskForm.is_resolved = task.is_resolved;
    updatedTaskForm.put(route('admin.tickets.task.resolve', task.id), { preserveScroll: false, preserveState: false })
}

const deleteTask = (task) => {
    updatedTaskForm.id = task.id;
    updatedTaskForm.delete(route('admin.tickets.task.delete', task.id), { preserveScroll: false, preserveState: false })
}

const editTaskName = (task) => {
    selectedInput.value = task.id;
    editData[task.id] = task.task_name;
};

const updateTaskName = (task, id) => {
    // Implement your update logic here
    console.log('Updated task name:', task);
    updateTask(task, id);
    // Reset input and selected input
    selectedInput.value = null;
    editData[task.id] = '';
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