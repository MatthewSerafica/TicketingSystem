<template>
    <div class="">
        <Header class="sticky-top" style="z-index: 110;"></Header>
        <div class="position-absolute end-0">
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
        <div class="container justify-content-center mt-4 col-6">
            <div class="row g-2 justify-content-center">
                <div class="input-group w-50 ">
                    <span class="input-group-text rounded-start-pill" id="searchIcon"><i
                            class="bi bi-search"></i></span>
                    <input type="text" class="form-control rounded-end-pill py-2" id="search" name="search"
                        v-model="search" placeholder="Search for posts..." aria-label="searchIcon"
                        aria-describedby="searchIcon" />
                </div>
                <div class="px-4 text-center bg-white border-bottom">
                    <div class="p-4 d-flex flex-column gap-2">
                        <button class="p-2 px-4 rounded-pill form-control btn btn-outline-secondary d-flex"
                            @click="showPost">What are you thinking about?</button>
                    </div>
                    <Post v-if="isShowPost" :technicians="technicians" :type="page.props.user.user_type" @closeDelete="closePost" />
                </div>
                <div v-if="posts.data.length" class="table-responsive rounded pt-2 px-2 mb-3">
                    <div class="bg-white border-bottom pb-2 mb-2" v-for="post in posts.data" v-bind:key="post.id">
                        <div class="post rounded px-3 pb-1">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <img v-if="post.user.avatar !== 'http://127.0.0.1:8000/storage'"
                                            :src="post.user.avatar" alt="User profile picture"
                                            class="avatar rounded-circle">
                                        <EmptyProfile v-else class="avatar rounded-circle">
                                        </EmptyProfile>
                                    </div>
                                    <div class="d-flex flex-row gap-2 mt-3">
                                        <div class="d-flex align-items-center justify-content-center gap-4">
                                            <p class="fw-semibold text-dark"><small>{{ post.user.name }}</small></p>
                                        </div>
                                        <div class="d-flex flex-row gap-2">
                                            <p class="text-secondary">
                                                •
                                            </p>
                                            <p class="text-secondary"><small>{{ post.time_since_posted }}</small></p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="page.props.user.id === post.user.id"
                                    class="mt-1 ellipsis rounded-circle dropdown">
                                    <button data-bs-toggle="dropdown" aria-expanded="false" class="btn rounded-circle">
                                        <i class="bi bi-three-dots-vertical text-dark"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <div class="dropdown-item" @click="showDelete(post)">Delete</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <a :href="route('admin.forum.post', [post.id])" class="text-decoration-none">
                                <div class="text-dark fw-light fst-italic" v-if="post.tagged.length">
                                    <p><small>Tagged:
                                            <span v-for="tag in post.tagged" class="me-2">
                                                {{ tag.user.name }}
                                            </span>
                                        </small>
                                    </p>
                                </div>

                                <p class="text-dark"><strong>{{ post.title }}</strong></p>

                                <div v-if="post.image" class="card mb-2">
                                    <img :src="'http://127.0.0.1:8000/storage/' + post.image" alt=""
                                        class="rounded image">
                                </div>

                                <p class="text-secondary-emphasis"> {{ post.content }} </p>

                                <div class="d-flex justify-content-between text-dark">
                                    <div class="d-flex align-items-center justify-content-center gap-2 rounded-pill p-2 comment"
                                        style="width: 4rem;">
                                        <i class="bi bi-chat-left-dots text-dark"></i>
                                        <span class="text-xs text-dark">{{ post.comment_count }}</span>
                                    </div>
                                </div>

                            </a>
                        </div>
                    </div>
                </div>
                <EmptyCard :title="'No Post yet...'" v-else class="mt-2 w-75" style="height:20rem;">
                </EmptyCard>
                <div ref="last" class="translate"></div>
                <div v-if="loading" class="text-center">
                    <span>Loading...</span>
                </div>
            </div>
        </div>

        <Delete v-if="isShowDelete" :post="selectedPost" @closeDelete="closeDelete" />
    </div>
</template>

<script setup>
import Toast from '@/Components/Toast.vue';
import EmptyCard from '@/Components/EmptyState/Post.vue';
import Button from '@/Components/Button.vue';
import Delete from "@/Components/DeleteModal.vue";
import Post from '@/Components/PostModal.vue';
import EmptyProfile from '@/Components/EmptyState/Profile.vue';
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { router, usePage } from '@inertiajs/vue3';
import { useIntersectionObserver } from '@vueuse/core';
import axios from 'axios';
import { ref, watch, watchEffect } from 'vue';
import Alpine from 'alpinejs';

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

let selectedPost = ref(null);

const isShowDelete = ref(false);

function closeDelete() {
    if (isShowDelete.value) {
        selectedPost.value = null
        isShowDelete.value = false;
    }
}

function showDelete(post) {
    if (!isShowDelete.value) {
        selectedPost.value = post;
        isShowDelete.value = true;
    }
}


const props = defineProps({
    posts: Object,
    filters: Object,
    technicians: Object,
})

const isShowPost = ref(false);

function closePost() {
    if (isShowPost.value) {
        isShowPost.value = false;
    }
}

function showPost() {
    if (!isShowPost.value) {
        isShowPost.value = true;
    }
}

let search = ref(props.filters.search);
let sortColumn = ref("created_at");
let sortDirection = ref("asc");
let timeoutId = null;

const fetchData = () => {
    router.get(
        route('admin.forum'),
        {
            search: search.value,
            sort: sortColumn.value,
            direction: sortDirection.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    )
}

const resetSorting = () => {
    console.log("Reset Sorting");
    sortColumn.value = "created_at"
    sortDirection.value = "asc"
}

const debouncedFetchData = () => {
    if (timeoutId) {
        clearTimeout(timeoutId)
    }
    timeoutId = setTimeout(() => {
        fetchData()
    }, 500)
}

watch(search, () => {
    if (!search.value) {
        resetSorting()
    }
    debouncedFetchData();
})


const last = ref(null)

const { stop } = useIntersectionObserver(last, ([{ isIntersecting }]) => {
    if (props.posts.data.length < 15) {
        stop();
        return;
    }

    if (!isIntersecting) {
        return
    }

    axios.get(`${props.posts.meta.path}?cursor=${props.posts.meta.next_cursor}`).then((response) => {
        props.posts.data = [...props.posts.data, ...response.data.data]
        props.posts.meta = response.data.meta

        if (!response.data.meta.next_cursor) {
            stop()
        }
    })
});
</script>

<style scoped>
.post:hover {
    background-color: #f0f0f0;
    cursor: pointer;

    .comment {
        background-color: #d4d4d4;
    }
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

.avatar {
    width: 2rem;
    /* Width of the avatar */
    height: 2rem;
    /* Height of the avatar */
    object-fit: cover;
    transition: transform 0.5s ease;
    cursor: pointer;
}

@media (max-width: 768px) {
    .container {
        width: 100%;
        padding: 0 15px;
    }

    .input-group {
        width: 100%;
    }

    .avatar {
        width: 40px;
        height: 40px;
    }
}


@media (max-width: 375px)    {
    .container {
            width: 100%;
            padding: 0 15px;
        }

        .input-group {
            width: 100%;
        }

        .avatar {
            width: 40px;
            height: 40px;
        }

        #search {
            width: 100%; /* Make the input full width */
        }

}

@media (max-width: 320px)    {
    .container {
            width: 100%;
            padding: 0 15px;
        }

        .input-group {
            width: 100%;
        }

        .avatar {
            width: 40px;
            height: 40px;
        }

        #search {
            width: 50%; /* Make the input full width */
        }

}
</style>