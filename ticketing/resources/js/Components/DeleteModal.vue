<template>
 
</template>

<script setup>
import { ref } from 'vue';


const { modal, hide } = useModal();
const modalId = 'deleteModal'; // You can customize the modal ID here

const title = ref('');
const message = ref('');
const confirmAction = ref(() => {});

const showModal = (modalTitle, modalMessage, action) => {
  title.value = modalTitle;
  message.value = modalMessage;
  confirmAction.value = action;
  modal.show(modalId);
};

const hideModal = () => {
  modal.hide(modalId);
};

</script>
