<template>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="notificationBar"
        aria-labelledby="notificationBarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="notificationBarLabel">Notifications</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="flex-column d-flex gap-2 list-unstyled" id="notifications">
            </ul>
        </div>
    </div>
</template>

<script setup>
</script>