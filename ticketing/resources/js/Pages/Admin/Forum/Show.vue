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
            <div v-if="fullscreenImage" class="fullscreen-modal d-flex flex-column">
                <div class="d-flex flex-row justify-content-end w-100 px-5">
                    <button type="button" class="btn-close btn btn-secondary bg-white rounded-circle p-2"
                        data-bs-dismiss="modal" aria-label="Close" @click="closeFullscreenImage"></button>
                </div>
                <div class="modal-content d-flex justify-content-center align-items-center ">
                    <img :src="fullscreenImage" alt="Fullscreen Image" class="rounded">
                </div>
            </div>
            <div class="container justify-content-center mt-4 col-6">
                <div class="row">
                    <!-- Post Section -->
                    <div class="pb-2 d-flex flex-column gap-2">
                        <div class="post rounded px-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center gap-2"
                                    style="margin-left: -2.5rem;">
                                    <div class="mt-3">
                                        <a :href="route('admin.forum')"
                                            class="btn btn-outline-secondary rounded-pill d-flex flex-row justify-content-center align-items-center"
                                            style="width: 2rem; height: 2rem;">
                                            <i class="bi bi-arrow-left"></i>
                                        </a>
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
                            <div>
                                <div class="text-dark fw-light fst-italic" v-if="post.tagged.length">
                                    <p><small>Tagged:
                                            <span v-for="tag in post.tagged" class="me-2">
                                                {{ tag.user.name }}</span>
                                        </small>
                                    </p>
                                </div>

                                <h4 class="text-dark"><strong>{{ post.title }}</strong></h4>

                                <div v-if="post.image" class="card mb-2">
                                    <div class="position-absolute" style="right: 10px; top: 10px; z-index: 100;">
                                        <div class="btn text-light rounded-circle" data-bs-dismiss="modal"
                                            aria-label="Close"
                                            @click="handleImageClick('http://127.0.0.1:8000/storage/' + post.image)">
                                            <i class="bi bi-arrows-angle-expand"></i>
                                        </div>
                                    </div>
                                    <img :src="'http://127.0.0.1:8000/storage/' + post.image" alt=""
                                        class="rounded image"
                                        @click="handleImageClick('http://127.0.0.1:8000/storage/' + post.image)">
                                </div>

                                <p class="text-secondary-emphasis"> {{ post.content }} </p>
                            </div>

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
                                        <textarea class="p-2 px-4 rounded-4 form-control mb-2"
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
                    <div v-if="comments.length" class="comment-container">
                        <div class="pb-2 d-flex flex-column commentt" v-for="comment in comments"
                            v-bind:key="comment.id">
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
                                        class="text-secondary-emphasis"> {{ comment.content }}
                                    </p>

                                    <!-- Edit Form -->
                                    <div v-if="isShowEdit && selectedComment.id === comment.id" class="">
                                        <form @submit.prevent="edit(comment.id)">
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
                                                    <li class="text-decoration-none" v-for="user in userData"
                                                        :key="user.id" @click="selectUser(user)">
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
                                                                    <button type="button"
                                                                        class="btn btn-sm rounded-circle"
                                                                        @click="removeMention(index)"
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
                                                                    @click="selectUser(user)">
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
                                                    <div v-if="more.parent_comment_id === reply.id"
                                                        class="post rounded">
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
                                                                        <EmptyProfile v-else
                                                                            class="avatar rounded-circle">
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
                                                                    <button data-bs-toggle="dropdown"
                                                                        aria-expanded="false"
                                                                        class="btn rounded-circle">
                                                                        <i
                                                                            class="bi bi-three-dots-vertical text-dark"></i>
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
                                                                    <div
                                                                        class="d-flex justify-content-end py-1 px-3 gap-2">
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
                    <EmptyCard v-else :title="'No Comments yet...'" class="mt-2 w-75" style="height:20rem;" />
                </div>
            </div>
        </div>

        <Delete v-if="isShowDelete" :comment="selectedComment" :type="page.props.user.user_type" @closeDelete="closeDelete" />
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Delete from "@/Components/DeleteModal.vue";
import EmptyCard from '@/Components/EmptyState/Comments.vue';
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import Toast from '@/Components/Toast.vue';
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Alpine from 'alpinejs';
import axios from 'axios';
import { ref, watch, watchEffect } from 'vue';

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

let showAutocomplete = ref(false);
let userData = ref([]);

const selectUser = (user) => {
    const userIndex = userData.value.findIndex((data) => !data.selectedUser);
    if (userIndex !== -1) {
        userData.value[userIndex].selectedUser = user.name;
        userData.value[userIndex].userId = user.id;
        commentForm.tagged_user_name.push(user.name);
        commentForm.tagged_user.push(user.id);

        const content = commentForm.content;
        const lastIndex = content.lastIndexOf('@');
        commentForm.content = content.substring(0, lastIndex) + '';

        showAutocomplete.value = false;

        userData.value = userData.value.filter((data, index) => index !== userIndex);
    }
};

const removeMention = (index) => {
    commentForm.tagged_user_name.splice(index, 1);
    commentForm.tagged_user.splice(index, 1);
};


watch(() => commentForm.tagged_user_name, (newValue, oldValue) => {
    console.log('new', newValue)
    console.log('old', oldValue);
    if (newValue !== oldValue) {
        showAutocomplete.value = true;
    }

    userData.value.push({
        selectedUser: ref(''),
        userId: null,
    });
});

watch(() => commentForm.content, (newValue, oldValue) => {
    if (oldValue && newValue.length < oldValue.length) {
        const removedWord = oldValue.substring(0, newValue.lastIndexOf('@'));
        console.log('removed word: ' + removedWord + '  ' + oldValue.substring(0, newValue.lastIndexOf('@')))
        const index = commentForm.tagged_user_name.lastIndexOf(removedWord.trim());
        if (index !== -1) {
            const userId = commentForm.tagged_user[index];
            commentForm.tagged_user.splice(index, 1);
            commentForm.tagged_user_name.splice(index, 1);
            userData.value = userData.value.filter(user => user.userId !== userId);
            console.log(commentForm.tagged_user, commentForm.tagged_user_name)
        }
    }
});

const handleContentChange = async (content) => {
    if (content.includes('@')) {
        const searchTerm = content.split('@').pop().trim();
        if (searchTerm.length > 0) {
            try {
                const response = await axios.get(`/admin/forum/post/users/${searchTerm}`);
                userData.value = response.data.users;
                showAutocomplete.value = true;
            } catch (error) {
                console.error('Error fetching user suggestions:', error);
            }
        }
    } else {
        showAutocomplete.value = false;
    }
};

const comment = () => commentForm.post(route('admin.forum.comment', [props.post.id, props.post.title]), { preserveScroll: false, preserveState: false })

const reply = (comment) => {
    selectedComment.value = comment;
    commentForm.post(route('admin.forum.reply', [props.post.id, props.post.title, selectedComment.value]), { preserveScroll: false, preserveState: false })
}

    const replyMore = (comment) => {
        selectedComment.value = comment;
        commentForm.post(route('admin.forum.reply', [props.post.id, props.post.title, selectedComment.value]), { preserveScroll: false, preserveState: false })
    }


const fullscreenImage = ref(null);

const openFullscreenImage = (imageSrc) => {
    fullscreenImage.value = imageSrc;
};

const closeFullscreenImage = () => {
    fullscreenImage.value = null;
};

const handleImageClick = (imageSrc) => {
    openFullscreenImage(imageSrc);
};

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

const removeMentionEdit = (index) => {
    editComment.tagged_user_name.splice(index, 1);
    editComment.tagged_user.splice(index, 1);
};

const selectUserEdit = (user) => {
    const userIndex = userData.value.findIndex((data) => !data.selectedUser);
    if (userIndex !== -1) {
        userData.value[userIndex].selectedUser = user.name;
        userData.value[userIndex].userId = user.id;
        editComment.tagged_user_name.push(user.name);
        editComment.tagged_user.push(user.id);

        const content = editComment.content;
        const lastIndex = content.lastIndexOf('@');
        editComment.content = content.substring(0, lastIndex) + '';

        showAutocomplete.value = false;

        userData.value = userData.value.filter((data, index) => index !== userIndex);
    }
};

watch(() => editComment.tagged_user_name, (newValue, oldValue) => {
    /* if (newValue !== oldValue) {
        showAutocomplete.value = true;
    } */

    userData.value.push({
        selectedUser: ref(''),
        userId: null,
    });
});

watch(() => editComment.content, (newValue, oldValue) => {
    if (oldValue && newValue.length < oldValue.length) {
        const removedWord = oldValue.substring(0, newValue.lastIndexOf('@'));
        console.log('removed word: ' + removedWord + '  ' + oldValue.substring(0, newValue.lastIndexOf('@')))
        const index = editComment.tagged_user_name.lastIndexOf(removedWord.trim());
        if (index !== -1) {
            const userId = editComment.tagged_user[index];
            editComment.tagged_user.splice(index, 1);
            editComment.tagged_user_name.splice(index, 1);
            userData.value = userData.value.filter(user => user.userId !== userId);
            console.log(editComment.tagged_user, editComment.tagged_user_name)
        }
    }
});

const handleContentChangeEdit = async (content) => {
    if (content.includes('@')) {
        const searchTerm = content.split('@').pop().trim();
        if (searchTerm.length > 0) {
            try {
                const response = await axios.get(`/admin/forum/post/users/${searchTerm}`);
                userData.value = response.data.users;
                showAutocomplete.value = true;
            } catch (error) {
                console.error('Error fetching user suggestions:', error);
            }
        }
    } else {
        showAutocomplete.value = false;
    }
};

const edit = (comment) => editComment.put(route('admin.forum.edit.comment', [comment]), { preserveScroll: false, preserveState: false });


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
</style>