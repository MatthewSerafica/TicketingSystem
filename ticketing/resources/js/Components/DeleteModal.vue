<template>
  <div class="custom-modal" @close="closeDelete">
    <div class="modal-content d-flex flex-column">
      <div class="d-flex flex-row justify-content-end">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeDelete"></button>
      </div>
      <div>
        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="red" class="bi bi-exclamation-diamond"
          viewBox="0 0 16 16">
          <path
            d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.48 1.48 0 0 1 0-2.098zm1.4.7a.495.495 0 0 0-.7 0L1.134 7.65a.495.495 0 0 0 0 .7l6.516 6.516a.495.495 0 0 0 .7 0l6.516-6.516a.495.495 0 0 0 0-.7L8.35 1.134z" />
          <path
            d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
        </svg>
        <h2 class="mt-2">Confirm Deletion</h2>
        <p v-if="department">Are you sure you want to delete <strong>{{ department.department }}</strong>?</p>
        <p v-if="office">Are you sure you want to delete <strong>{{ office.office }}</strong>?</p>
        <p v-if="post">Are you sure you want to delete your post?</p>
        <p v-if="comment">Are you sure you want to delete your comment?</p>
        <p v-if="problem">Are you sure you want to delete <strong>{{ problem.problem }}</strong>?</p>
      </div>
      <div class="d-flex flex-row gap-2 justify-content-end mt-2">
        <Link v-if="department" method="delete" type="button" as="button" :href="route('admin.department.delete', { id: department.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <Link v-if="office" method="delete" type="button" as="button" :href="route('admin.office.delete', { id: office.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <Link v-if="service" method="delete" type="button" as="button" :href="route('admin.services.delete', { id: service.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <Link v-if="problem" method="delete" type="button" as="button" :href="route('admin.problems.delete', { id: problem.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <Link v-if="post" method="delete" type="button" as="button" :href="route('technician.forum.delete', { id: post.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <Link v-if="comment && type === 'technician'" method="delete" type="button" as="button" :href="route('technician.forum.delete.comment', { id: comment.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <Link v-if="commentTicket && type === 'admin'" method="delete" type="button" as="button" :href="route('admin.ticket.delete.comment', { id: commentTicket.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <Link v-if="commentTicket && type === 'technician'" method="delete" type="button" as="button" :href="route('technician.ticket.delete.comment', { id: commentTicket.id })"
          @click="closeDelete" class="btn btn-danger">Delete</Link>
        <button @click="closeDelete" type="button" as="button" class="btn btn-outline-secondary ">Cancel</button>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { Link } from '@inertiajs/vue3';
import { defineEmits } from 'vue';

const emit = defineEmits(['closeDelete'])

const closeDelete = () => {
  emit('closeDelete')
}

const props = defineProps({
  department: Object,
  office: Object,
  service: Object,
  post: Object,
  comment: Object,
  commentTicket: Object,
  problem: Object,
  type: Object,
})

</script>
  
<style scoped>
.custom-modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: #fff;
  padding: 30px;
  border-radius: 8px;
  text-align: center;
  width: 25%;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  cursor: pointer;
}
</style>
  
