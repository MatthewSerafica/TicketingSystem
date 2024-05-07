<template>
    <div>
        <Header></Header>

        <div class="d-flex justify-content-center flex-column align-content-center align-items-center main-content">
            <!--CTAs and Search-->
            <div class="text-center justify-content-center align-items-center d-flex mt-3 flex-column gap-3">
                <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                    <h1 class="fw-bold">Log Report</h1>
                </div>

                <div class="table-responsive" style="width: 75rem;">
                    <div v-if="logs.data.length" class="d-flex justify-content-end mb-2">
                        <pagination :links="logs.links" :key="'logs'" />
                        <br>
                    </div>
                    <table class="table table-hover shadow custom-rounded-table">
                        <thead>
                            <tr class="text-start">
                                <th class="text-muted">ID</th>
                                <th class="text-muted">User</th>
                                <th class="text-muted">User Type</th>
                                <th class="text-muted w-50">Actions Taken</th>
                                <th class="text-muted">Date Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="log in logs.data" class="align-middle text-start">
                                <td>{{ log.id }}</td>
                                <td>{{ log.name }}</td>
                                <td>{{ log.user_type }}</td>
                                <td>{{ log.actions_taken }}</td>
                                <td>{{ formatDate(log.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/AdminHeader.vue";
import pagination from "@/Components/Pagination.vue";
import moment from "moment";

const props = defineProps({
    logs: Object,
})

const formatDate = (date) => {
    return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};
</script>