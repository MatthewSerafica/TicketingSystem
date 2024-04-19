<template>
    <div>
        <Header class="sticky-top" style="z-index: 110;"></Header>
        <div class="position-absolute end-0 mt-3 me-3" style="z-index: 100;">
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
            <div class="container justify-content-center mt-4 col-6">
                <div class="row">
                    <!-- Post Section -->
                    <div class="pb-2 d-flex flex-column gap-2">
                        <div class="post rounded px-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center gap-2"
                                    style="margin-left: -2.5rem;">
                                    <div class="mt-3">
                                        <Link :href="route('technician.forum')"
                                            class="btn btn-outline-secondary rounded-pill d-flex flex-row justify-content-center align-items-center"
                                            style="width: 2rem; height: 2rem;">
                                        <i class="bi bi-arrow-left"></i>
                                        </Link>
                                    </div>
                                    <div class="mt-3 d-flex align-items-center gap-2">
                                        <div class="">
                                            <img v-if="post.user.avatar !== 'http://127.0.0.1:8000/storage'"
                                                :src="post.user.avatar" alt="User profile picture"
                                                class="avatar rounded-circle">
                                            <EmptyProfile v-else class="avatar rounded-circle">
                                            </EmptyProfile>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-3">
                                            <p class="fw-semibold text-dark"><small>{{ post.user.name }}</small></p>
                                            <p class="text-secondary">
                                                •
                                            </p>
                                            <p class="text-secondary"><small>{{ post.time_since_posted }}</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="post.tagged_user">
                                <p><small>Tagged: {{ post.tagged_user }}</small></p>
                            </div>

                            <h4 class="text-dark"><strong>{{ post.title }}</strong></h4>

                            <p class="text-secondary-emphasis"> {{ post.content }} </p>

                            <div class="d-flex justify-content-between text-dark">
                                <div class="d-flex align-items-center justify-content-center gap-2 rounded-pill p-2 comment"
                                    style="width: 4rem;">
                                    <i class="bi bi-chat-left-dots"></i>
                                    <span class="text-xs">{{ post.comment_count }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center bg-white">
                            <div class="d-flex flex-column gap-2">
                                <button v-if="!isShowComment"
                                    class="p-2 px-4 rounded-pill form-control border cursor-text"
                                    @click="showComment">Add a comment</button>
                                <div v-if="isShowComment" class="d-flex flex-column gap-2">
                                    <form @submit.prevent="comment">
                                        <textarea class="p-2 px-4 rounded-4 form-control"
                                            v-model="commentForm.content"></textarea>
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

                    <!-- Comment Section -->
                    <div class="comment-container">
                        <div class="pb-2 d-flex flex-column commentt" v-for="comment in comments"
                            v-bind:key="comment.id">
                            <div class="post rounded px-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="mt-1 d-flex align-items-center gap-2">
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
                                <div class="comment-line-connector-container">
                                    <div class="comment-line-connector"></div>
                                    <div class="comment-line-connector-sideways"></div>
                                    <!-- Comment Content -->
                                    <div>
                                        <div v-if="comment.tagged_user">
                                            <p><small>Tagged: {{ comment.tagged_user }}</small></p>
                                        </div>
                                        <p class="text-secondary-emphasis"> {{ comment.content }} </p>

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
                                        </div>

                                        <div v-if="isShowReply && selectedComment && selectedComment === comment.id"
                                            class="d-flex flex-column gap-2 border rounded-4 p-2">
                                            <form @submit.prevent="reply(comment.id)">
                                                <textarea ref="commentTextarea"
                                                    class="p-2 px-4 rounded-4 form-control border-0"
                                                    v-model="commentForm.content"></textarea>
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
                            </div>

                            <!-- Reply Section -->
                            <div v-if="repliesVisibility[comment.id]" class="reply-container">
                                <div v-for="reply in replies" v-bind:key="reply.id" class="mx-5 replyy">
                                    <div v-if="reply.parent_comment_id === comment.id" class="post rounded">
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
                                                <div v-if="reply.tagged_user">
                                                    <p><small>Tagged: {{ reply.tagged_user }}</small></p>
                                                </div>
                                                <p class="text-secondary-emphasis"> {{ reply.content }} </p>

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
                                                </div>
                                                <div v-if="isShowReply && selectedComment && selectedComment === reply.id"
                                                    class="d-flex flex-column gap-2 border rounded-4 p-2">
                                                    <form @submit.prevent="replyMore(reply.id)">
                                                        <textarea class="p-2 px-4 rounded-4 form-control border-0"
                                                            v-model="commentForm.content"></textarea>
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
                                        </div>

                                        <!-- More Replies Section -->
                                        <div v-if="repliesVisibility[reply.id]" class="replies-container">
                                            <div v-for="more in replies" v-bind:key="more.id" class="mx-5">
                                                <div v-if="more.parent_comment_id === reply.id" class="post rounded">
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
                                                        </div>
                                                    </div>

                                                    <div class="">
                                                        <div v-if="more.tagged_user">
                                                            <p><small>Tagged: {{ more.tagged_user }}</small></p>
                                                        </div>
                                                        <p class="text-secondary-emphasis"> {{ more.content }} </p>
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
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Toast from '@/Components/Toast.vue';
import Header from '@/Pages/Layouts/TechnicianHeader.vue';
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Alpine from 'alpinejs';
import { onMounted, ref, watchEffect } from 'vue';

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
    post: Object,
    comments: Object,
    replies: Object,
})

const commentTextarea = ref(null);

const isShowComment = ref(false);

function closeComment() {
    if (isShowComment.value) {
        isShowComment.value = false;
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
    tagged_user: null,
})


const comment = () => commentForm.post(route('technician.forum.comment', [props.post.id, props.post.title]), { preserveScroll: false, preserveState: false })

const reply = (comment) => {
    selectedComment.value = comment;
    commentForm.post(route('technician.forum.reply', [props.post.id, props.post.title, selectedComment.value]), { preserveScroll: false, preserveState: false })
}

const replyMore = (comment) => {
    selectedComment.value = comment;
    commentForm.post(route('technician.forum.reply', [props.post.id, props.post.title, selectedComment.value]), { preserveScroll: false, preserveState: false })
}

const commenterNameElement = ref(null);

// Function to adjust the position of the line-connector-sideways
function adjustLineConnectorSideways() {
    // Get the bounding box of the commenter's name element
    const rect = commenterNameElement.value.getBoundingClientRect();

    // Calculate the desired top and left positions based on the commenter's name
    const desiredTop = rect.top; // Adjust this based on your layout
    const desiredLeft = rect.left; // Adjust this based on your layout

    // Set the new CSS properties for the line-connector-sideways
    const lineConnectorSideways = document.querySelector('.line-connector-sideways');
    lineConnectorSideways.style.top = `${desiredTop}px`;
    lineConnectorSideways.style.left = `${desiredLeft}px`;
}

// Call this function to adjust the position when the component is mounted
onMounted(() => {
    adjustLineConnectorSideways();
});

</script>

<style scoped>
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
    /* Width of the avatar */
    height: 2rem;
    /* Height of the avatar */
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}
</style>