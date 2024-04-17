<template>
    <div>
        <Header></Header>
        <!--Toast Render-->
        <div class="position-absolute end-0 mt-3 me-3" style="z-index: 100;">
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
        <!--Main Content-->
        <div class="d-flex justify-content-center flex-column align-content-center align-items-center main-content">
            <!--CTAs and Search-->
            <div class="text-center justify-content-center align-items-center d-flex mt-3 flex-column gap-3">
                <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                    <h1 class="fw-bold">Generate Reports</h1>
                </div>

                <div class="d-flex gap-2 rounded w-50">
                    <VueDatePicker v-model="date" range multi-calendars :max-date="new Date()" teleport-center
                        placeholder="Select Date" class="border rounded border-1" />
                </div>

                <div v-if="monthsAndYears.length > 0" class="table-responsive rounded shadow pt-2 px-2"
                    style="width: 50rem;">
                    <div class="">
                        <table class="table table-hover custom-rounded-table">
                            <thead>
                                <tr class="text-start">
                                    <th class="text-start text-muted" @click="handleSort('ticket_number')">
                                        No
                                    </th>
                                    <th class="text-muted">Month/Year</th>
                                    <th class="text-center text-muted">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(monthYear, index) in monthsAndYears" :key="index" class="align-middle">
                                    <td class="text-start">{{ index + 1 }}</td>
                                    <td class="text-start fw-bold">
                                        <div>
                                            {{ formatMonthYear(monthYear.month, monthYear.year) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center align-items-center">
                                            <Button :name="'Download'" :color="'primary'"
                                                @click="download(monthYear.month, monthYear.year)"></Button>
                                            <Link class="btn btn-outline-primary"
                                                :href="route('admin.reports.generate-report.print', { year: monthYear.year, month: monthYear.month })">
                                            Print
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <EmptyCard :title="'No reports yet...'" v-else class="mt-2" style="height:20rem;">
                </EmptyCard>
            </div>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import EmptyCard from '@/Components/EmptyState/Table.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router } from "@inertiajs/vue3";
import moment from "moment";
import { ref, watch } from 'vue';


const props = defineProps({
    monthsAndYears: Object,
    tickets: Object,
})

const formatMonthYear = (month, year) => {
    const formattedDate = moment(`${year}-${month}`, 'YYYY-MM').format('MMMM YYYY');
    return formattedDate;
};

function download(month, year) {
    const monthName = moment(`${year}-${month}-01`, 'YYYY-MM-DD').format('MMMM');
    const data = props.tickets.filter(ticket => {
        const ticketDate = moment(ticket.created_at, 'YYYY-MM-DD');
        return ticketDate.month() + 1 === month && ticketDate.year() === year;
    });
    const csvContent = convertToCSV(data);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `RS System ${monthName} ${year}.csv`);
    link.click();
}


function convertToCSV(data) {
    const headerMap = {
        ticket_number: 'Ticket No.',
        created_at: 'Date',
        rr_no: 'RR No.',
        ms_no: 'MS No.',
        rs_no: 'RS No.',
        employee: 'Employee',
        department: 'Office',
        issue: 'Issue',
        technicians: 'Technician',
        sr_no: 'SR No.',
        resolved_at: 'Date Done',
        remarks: 'Remarks'
    };

    const headers = Object.keys(headerMap);
    const rows = data.map(ticket => headers.map(header => {
        if (header === 'employee') {
            // Retrieve employee.user.name if it exists
            const employeeName = ticket.employee?.user?.name || '';
            return employeeName;
        } else if (header === 'department') {
            return `${ticket.employee.department} - ${ticket.employee.office}`;
        } else if (header === 'created_at' || header === 'resolved_at') {
            const value = ticket[header];
            if (value) {
                const formatted = moment(value).format('MM/DD/YYYY');
                return formatted;
            } else {
                return value;
            }
        } else {
            const value = ticket[header];
            return typeof value === 'string' && value.includes(',') ? `"${value}"` : value;
        }
    }));

    const headerRow = headers.map(header => headerMap[header]).join(',');
    const csvRows = [headerRow, ...rows.map(row => row.join(','))];
    return csvRows.join('\n');
}

let from_date_filter = ref(null)
let to_date_filter = ref(null)
let timeoutId = null;

function fetchData() {
    const url = route('admin.reports.generate-report.show', {
        from: from_date_filter,
        to: to_date_filter,
    })

    router.visit(url);

}

const debouncedFetchData = () => {
    if (timeoutId) {
        clearTimeout(timeoutId)
    }
    timeoutId = setTimeout(() => {
        fetchData()
    }, 500)
}

const date = ref();

watch(date, (newDate) => {
    if (newDate) {
        from_date_filter = moment(newDate[0]).format('YYYY-MM-DD');
        to_date_filter = moment(newDate[1]).format('YYYY-MM-DD');
    } else {
        from_date_filter = null;
        to_date_filter = null;
    }
    if (!newDate) {
        return
    }
    debouncedFetchData();
});



</script>