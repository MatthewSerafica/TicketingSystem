<template>
    <div>
        <div class="d-flex flex-row justify-content-between p-2 print-hidden">
            <div class="print-hidden">
                <Link :href="route('admin.reports.generate-report')"
                    class="print-hidden btn btn-secondary m-2 d-flex flex-row justify-content-start align-items-center"
                    style="width: 6rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-caret-left-fill print-hidden" viewBox="0 0 16 16">
                    <path
                        d="m3.86 8.753 5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z" />
                </svg>
                <span class="print-hidden">Back</span>
                </Link>
            </div>
            <div class="print-hidden">
                <button class="btn btn-primary print-hidden" @click="printPage">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                        class="bi bi-printer-fill" viewBox="0 0 16 16">
                        <path
                            d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1" />
                        <path
                            d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="table-responsive px-3 rounded shadow pt-2 px-2">
            <table class="table table-hover custom-rounded-table">
                <thead>
                    <tr class="text-start text-muted">
                        <th class="text-start text-muted">No</th>
                        <th class="text-muted">Date</th>
                        <th class="text-center text-muted">RR No</th>
                        <th class="text-center text-muted">MS No</th>
                        <th class="text-center text-muted">RS No</th>
                        <th class="text-muted">Client</th>
                        <th class="text-muted">Request</th>
                        <th class="text-muted">Service</th>
                        <th class="text-muted">Technician</th>
                        <th class="text-center text-muted">SR No</th>
                        <th class="text-muted">Date Resolved</th>
                        <th class="text-muted">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ticket in tickets" class="align-middle">
                        <td class="text-center">{{ ticket.ticket_number }}</td>
                        <td class="text-start">{{ moment(ticket.created_at).format("MMM DD, YYYY") }}</td>
                        <td class="text-center">{{ ticket.rr_no }}</td>
                        <td class="text-center">{{ ticket.ms_no }}</td>
                        <td class="text-center">{{ ticket.rs_no }}</td>
                        <td class="text-start">{{ ticket.employee }}</td>
                        <td class="text-start" style="max-width: 15rem;">{{ ticket.description }}</td>
                        <td class="text-start">{{ ticket.service }}</td>
                        <td class="text-start" style="max-width:8rem;">{{ ticket.technicians }}</td>
                        <td class="text-center">{{ ticket.sr_no }}</td>
                        <td class="text-start">{{
                    ticket.resolved_at ? moment(ticket.resolved_at).format("MMM DD, YYYY") : null }}
                        </td>
                        <td class="text-start">{{ ticket.remarks }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import moment from "moment";

const props = defineProps({
    tickets: Object,
})

const printPage = () => {
    window.print();
}
</script>

<style scoped>
@media print {
    .print-hidden {
        display: none;
    }
}
</style>