<template>
    <div class="">
        <Header class="sticky-top" style="z-index: 110;"></Header>

        <div class="container justify-content-center mt-4 col-6">
            <div class="row g-2">
                <div class="p-4 text-center bg-white border-bottom">
                    <div class="p-4 d-flex flex-column gap-2">
                        <button class="p-2 px-4 rounded-pill form-control btn btn-outline-secondary d-flex"
                            @click="showPost">What are you thinking about?</button>
                    </div>
                    <Post v-if="isShowPost" @closeDelete="closePost" />
                </div>
                <div class="bg-white border-bottom pb-2 mb-2" v-for="post in posts" v-bind:key="post.id">
                    <Link :href="route('technician.forum.post', [post.id, post.title])" class="text-decoration-none">
                    <div class="post rounded p-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center justify-content-center gap-4">
                                <p class="fw-semibold text-dark"><small>{{ post.user.name }}</small></p>
                            </div>
                            <p class="text-secondary">
                                •
                            </p>
                            <p class="text-secondary"><small>{{ post.time_since_posted }}</small></p>
                        </div>
                        <div v-if="post.tagged_user">
                            <p><small>Tagged: {{ post.tagged_user }}</small></p>
                        </div>

                        <p class="text-dark"><strong>{{ post.title }}</strong></p>

                        <p class="text-secondary-emphasis"> {{ post.content }} </p>

                        <div class="d-flex justify-content-between text-dark">
                            <div class="d-flex align-items-center justify-content-center gap-2 rounded-pill p-2 comment" style="width: 4rem;">
                                <i class="bi bi-chat-left-dots"></i>
                                <span class="text-xs">{{post.comment_count}}</span>
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Post from '@/Components/PostModal.vue';
import Header from '@/Pages/Layouts/TechnicianHeader.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    posts: Object,
    filters: Object,
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
</style>