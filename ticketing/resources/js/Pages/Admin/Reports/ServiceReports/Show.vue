<template>
    <div>
        <Header></Header>
        <div class="mt-3">
            <form @submit.prevent="edit">
                <br />
                <div class="container">
                    <div class="title-container text-center mb-2">
                        <h1 class="fw-bold">Create Service Reports</h1>
                    </div>
                    <div class="create-report">
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">SR-A:</span>
                                    <input type="text" class="form-control" id="service_id" v-model="form.service_id"
                                        required disabled>
                                </div>
                                <span v-if="form.errors.service_id" class="error-message">
                                    {{ form.errors.service_id }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="dateStarted" class="form-label">Date Started:</label>
                                <input type="date" class="form-control" id="dateStarted" v-model="form.date_started"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="timeStarted" class="form-label">Time Started:</label>
                                <input type="time" class="form-control" id="timeStarted" v-model="form.time_started"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column">
                                    <label for="ticketNumber" class="form-label">Ticket Number:</label>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-secondary" style="width: 70%"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ form.ticket_number ? form.ticket_number : 'Select a ticket number...' }}
                                        </button>
                                        <button type="button"
                                            class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="visually-hidden">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-item">
                                                Select a Ticket No.
                                            </li>
                                            <li v-for="ticket in tickets">
                                                <div class="dropdown-item" @click="ticketDetails(ticket)">
                                                    {{ ticket.ticket_number }}
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <span v-if="form.errors.ticket_number" class="error-message">
                                        {{ form.errors.ticket_number }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="technician" class="form-label">Technicians:</label>
                                <textarea class="form-control" id="technician" v-model="form.technician"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="requestingOffice" class="form-label">Requesting Office:</label>
                                <input type="text" class="form-control" id="requestingOffice"
                                    v-model="form.requesting_office">
                            </div>
                            <div class="col-md-4">
                                <label for="equipment_no" class="form-label">Equipment, Property Tag/Serial No.:</label>
                                <input type="text" class="form-control" id="equipment_no" v-model="form.equipment_no">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="problemEncountered" class="form-label">Problem Encountered:</label>
                                <input type="text" id="problemEncountered" v-model="form.issue" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="action" class="form-label">Action Taken:</label>
                                <select class="form-select" id="action" v-model="form.action">
                                    <option disabled>Assign action taken</option>
                                    <option v-for="service in services" :value="service.service">
                                        {{ service.service }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="recommendation" class="form-label">Recommendation:</label>
                                <input type="text" class="form-control" id="recommendation"
                                    v-model="form.recommendation">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="dateDone" class="form-label">Date Done:</label>
                                <input type="date" class="form-control" id="dateDone" v-model="form.date_done">
                            </div>
                            <div class="col-md-4">
                                <label for="timeDone" class="form-label">Time Done:</label>
                                <input type="time" class="form-control" id="timeDone" v-model="form.time_done">
                            </div>
                            <div class="col-md-4">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea type="text" class="form-control" id="remarks"
                                    v-model="form.remarks"></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <Button :name="'Update'" :color="'primary'"
                                        :disabled="!form.service_id || !form.date_started || !form.time_started || !form.ticket_number || !form.requesting_office || !form.equipment_no || !form.issue || !form.action || !form.recommendation || !form.date_done || !form.time_done"></Button>
                                    <Link :href="'/admin/reports/service-report'" class="btn btn-outline-secondary">
                                    Cancel</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Header from '@/Pages/Layouts/AdminHeader.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref, watch } from "vue";

const props = defineProps({
    technicians: Object,
    service_report: Object,
    services: Object,
    tickets: Object,
})

const form = useForm({
    service_id: props.service_report.service_id,
    date_started: props.service_report.date_started,
    time_started: props.service_report.time_started,
    ticket_number: props.service_report.ticket_number,
    technician: [props.service_report.technician],
    requesting_office: props.service_report.requesting_office,
    equipment_no: props.service_report.equipment_no,
    issue: props.service_report.issue,
    action: props.service_report.action,
    recommendation: props.service_report.recommendation,
    date_done: props.service_report.date_done,
    time_done: props.service_report.time_done,
    remarks: props.service_report.remarks,
});

const today = new Date();
const minDate = today.toISOString().split('T')[0];
let selectedTicket = ref([]);

onMounted(async () => {
    if (form.ticket_number) {
        const ticketSelected = form.ticket_number;
        selectedTicket = ticketSelected;
        const response = await fetch(route('admin.tickets.assigned', { id: form.ticket_number }));
        const data = await response.json();
        const technicianNames = [];
        const technicianId = [];

        for (let i = 0; i < data.length; i++) {
            technicianNames.push(data[i].technician[0].user.name);
            technicianId.push(data[i].technician[0].technician_id);
        }
        const formattedTechnicianNames = technicianNames.join(' / ');
        form.technician = formattedTechnicianNames;
        form.technicianId = technicianId;
    }
});
watch(() => form.ticket_number, async (newValue) => {
    const selectedTicket = props.tickets.find(ticket => ticket.ticket_number === newValue);

    console.log(selectedTicket.ticket_number)
    if (selectedTicket) {
        form.action = selectedTicket.service;
        form.issue = selectedTicket.issue;
        form.requesting_office = selectedTicket.employee.department + ' - ' + selectedTicket.employee.office;

        const response = await fetch(route('admin.tickets.assigned', { id: selectedTicket.ticket_number }));
        const data = await response.json();
        const technicianNames = [];
        const technicianId = [];

        for (let i = 0; i < data.length; i++) {
            technicianNames.push(data[i].technician[0].user.name);
            technicianId.push(data[i].technician[0].technician_id);
        }

        const formattedTechnicianNames = technicianNames.join(' / ');
        form.technician = formattedTechnicianNames;
    }
})

const ticketDetails = (ticket) => {
    if (ticket) {
        selectedTicket = null;
        form.ticket_number = ticket.ticket_number;
        selectedTicket = ticket;
    }
}

const edit = () => form.put(route('admin.reports.service-report.update', [form.service_id]), { preserveScroll: false, preserveState: false });
</script>


<style scoped>
.error-message {
    color: red;
}

.input-group {
    width: 100%;
}

.form-control {
    width: 100%;
}
</style>