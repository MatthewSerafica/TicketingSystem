<template>
    <Header></Header>
    <div class="recent">
        <div class="d-flex">
            <div class="recent-tickets">
                <h2>Recent Tickets</h2>
                <div class="d-flex gap-2">
                    <Button class="rounded" value="Create Ticket"></Button>
                    <Button class="rounded bg-secondary text-white" value="View All"></Button>
                </div>
            </div>
            <div class="cards">
                <Card></Card>
                <Card></Card>
                <Card></Card>
            </div>
        </div>
    </div>
</template>
<script setup>
import Header from '@/Pages/Layouts/AdminHeader.vue'
import Card from '@/Components/Cards.vue'
import Button from '@/Components/Button.vue'

const props = defineProps({
    test: String,
})


</script>

<style>
* {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
.recent {
    width: auto;
    padding: 3rem;
    padding-left: 15rem;
    justify-content: center;
    align-items: center;
    gap: 1rem;
}

.recent-tickets {
    display: flex;
    width: 520px;
    height: 604px;
    flex-direction: column;
    justify-content: center;
    align-items: left;
    gap: 1rem;
    flex-shrink: 0;
}

.cards {
    display: flex;
    width: 520px;
    height: 604px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
    flex-shrink: 0;
}
</style>