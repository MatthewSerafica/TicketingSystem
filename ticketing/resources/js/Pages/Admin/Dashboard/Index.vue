<template>
    <div class="justify-content-center">
        <Header></Header>
        <div class="justify-content-center align-items-center d-flex flex-column mt-5 pt-5 gap-5 main-content">
            <div class="gap-5 justify-content-center align-items-center recent">
                <div class="cta">
                    <h2 class="fw-semibold">Recent Tickets</h2>
                    <div class="d-flex gap-2">
                        <Link class="text-decoration-none" :href="route('admin.tickets.create')">
                        <Button :name="'Create'" :color="'primary'" class="btn-width shadow"></Button>
                        </Link>
                        <Link class="text-decoration-none" :href="route('admin.tickets')">
                        <Button :name="'View All'" :color="'outline-primary'" class="btn-width shadow"></Button>
                        </Link>
                    </div>
                </div>
                <div class="d-flex flex-column gap-4 justify-content-center align-items-center ms-5">
                    <div v-if="tickets && tickets.length > 0"
                        class="d-flex flex-column gap-4 justify-content-center align-items-center">
                        <div class="" v-for="ticket in tickets" :key="tickets.ticket_number">
                            <Link class="text-decoration-none" :href="route('admin.tickets')">
                            <Card class="text-truncate hover" :no="ticket.ticket_number" :issue="ticket.issue"
                                :employee="ticket.employee.user.name" :department="ticket.employee.department"
                                :date="formatDate(ticket.created_at)" :status="ticket.status"
                                :technicians="ticket.assigned ? ticket.assigned : 'Unassigned'">
                            </Card>
                            </Link>
                        </div>
                    </div>
                    <div v-else>
                        <EmptyCard :title="'No tickets today...'"></EmptyCard>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column w-100 justify-content-center align-items-center text-center">
                <h1 class="stat-label">Statistics</h1>
                <div class="w-100 justify-content-center align-items-center gap-5 m-3 statistics">
                    <div class="card p-5 shadow-sm">
                        <Doughnut :service="service" class="doughnut"></Doughnut>
                    </div>
                    <div class="card p-5 shadow-sm">
                        <Bar :yearly_data="yearly_data" class="bar"></Bar>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Cards.vue';
import EmptyCard from '@/Components/EmptyState/Cards.vue';
import EmptyData from '@/Components/EmptyState/Statistics.vue';
import Bar from "@/Pages/Admin/Dashboard/Charts/Bar.vue";
import Doughnut from "@/Pages/Admin/Dashboard/Charts/Doughnut.vue";
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { Link } from '@inertiajs/vue3';
import moment from "moment";
import { ref, watch } from 'vue';

const props = defineProps({
    tickets: Object,
    yearly_data: Object,
    service: Object,
})

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

const myData = ref(null);

watch(myData, (newValue, oldValue) => {
    // Perform actions based on data changes
    console.log('Data changed:', newValue);
});

</script>

<style scoped>

.hover:hover {
    background-color: white;
    transition: transform 0.5s ease;
    transform: scale(1.1); 
}

.btn-width {
    width: 150px;
    height: 50px;
}

.doughnut {
    width: 20rem;
}

.statistics {
    display: flex;
    flex-direction: row;
}

.recent {
    display: flex;
    flex-direction: row;
}

.bar {
    width: 40rem;
}

@media (max-width: 1024px) {
    .main-content {
        padding: 2rem;
    }

    .statistics {
        display: flex;
        flex-direction: column;
    }

    .doughnut {
        width: 30rem
    }

    .bar {
        width: 30rem;
    }

}

@media (max-width: 768px) {
    .main-content {
        padding-left: 20rem;
        width: 50%;
    }

    .recent {
        padding-left: 4rem;
        display: flex;
        flex-direction: column;
    }

    .cta {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-left: 3rem;
    }


    .statistics {
        padding-left: 6rem;
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        padding-left: 6rem;
    }

    .doughnut {
        width: 30rem
    }

    .bar {
        padding: 2rem
    }

}

@media (max-width: 425px) {
    .main-content {
        padding-left: 16.5rem;
        width: 25%;
    }

    .recent {
        padding-left: 4rem;
        display: flex;
        flex-direction: column;
    }

    .cta {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-left: 3rem;
    }


    .statistics {
        padding-left: 6rem;
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        padding-left: 6rem;
    }

    .doughnut {
        width: 30rem
    }

    .bar {
        width: 30rem;
    }

}

@media (max-width: 375px) {
    .main-content {
        padding-left: 15.5rem;
    }

    .recent {
        padding-left: 4rem;
        display: flex;
        flex-direction: column;
    }

    .cta {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-left: 3rem;
    }

    .statistics {
        padding-left: 6rem;
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        padding-left: 6rem;
    }

    .doughnut {
        width: 30rem
    }

    .bar {
        width: 30rem
    }

}

@media (max-width: 375px) {
    .main-content {
        padding-left: 14rem;
    }

    .recent {
        padding-left: 4rem;
        display: flex;
        flex-direction: column;
    }

    .cta {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding-left: 3rem;
    }

    .statistics {
        padding-left: 6rem;
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        padding-left: 6rem;
    }

    .doughnut {
        width: 30rem
    }

    .bar {
        width: 30rem
    }

}
</style>