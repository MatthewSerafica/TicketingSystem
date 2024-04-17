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
        <div class="text-center fw-bold mb-2">
            RS System (Monitoring) <br> {{ moment(from).format("MMM DD, YYYY") }} - {{ moment(to).format("MMM DD, YYYY")
            }}
        </div>

        <div class="table-responsive px-3 rounded pt-2 px-2">
            <table class="table table-hover custom-rounded-table border shadow-sm">
                <thead>
                    <tr class="text-start text-muted">
                        <th class="text-start text-muted">No</th>
                        <th class="text-muted">Date</th>
                        <th class="text-center text-muted">RR No</th>
                        <th class="text-center text-muted">MS No</th>
                        <th class="text-center text-muted">RS No</th>
                        <th class="text-muted">Client</th>
                        <th class="text-muted">Office</th>
                        <th class="text-muted">Request</th>
                        <th class="text-muted">Technician</th>
                        <th class="text-center text-muted">SR No</th>
                        <th class="text-muted">Date Done</th>
                        <th class="text-muted">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ticket in tickets" class="align-middle">
                        <td class="text-center">{{ ticket.ticket_number }}</td>
                        <td class="text-start">{{ moment(ticket.created_at).format("MM/DD/YYYY") }}</td>
                        <td class="text-center">{{ ticket.rr_no }}</td>
                        <td class="text-center">{{ ticket.ms_no }}</td>
                        <td class="text-center">{{ ticket.rs_no }}</td>
                        <td class="text-start">{{ ticket.employee.user.name }}</td>
                        <td class="text-start">{{ ticket.employee.department }} - {{ ticket.employee.office }}</td>
                        <td class="text-start" style="max-width: 15rem;">{{ ticket.issue }}</td>
                        <td class="text-start" style="max-width:8rem;">
                            <div v-for="(assignedTech, index) in ticket.assigned" :key="index">
                                <div v-for="(tech, techIndex) in assignedTech.technician" :key="techIndex">
                                    {{ tech.user.name ? tech.user.name : 'N/A' }}
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ ticket.sr_no }}</td>
                        <td class="text-start">
                            {{ ticket.resolved_at ? moment(ticket.resolved_at).format("MM/DD/YYYY") : null }}
                        </td>
                        <td class="text-start" style="max-width: 10rem;">{{ ticket.remarks }}</td>
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
    from: Object,
    to: Object,
})

const monthName = (month) => {
    const date = new Date(2000, month - 1, 1);
    return date.toLocaleString('en-US', { month: 'long' });
};

const printPage = () => {
    window.print();
}
</script>

<style scoped>
@media print {
    .print-hidden {
        display: none;
    }

    @page {
        size: auto;
        margin: 0;
    }
}
</style>