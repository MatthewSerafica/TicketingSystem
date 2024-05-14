<template>
    <div>
        <Header></Header>
        <div class="mt-3">
            <form @submit.prevent="edit">
                <br />
                <div class="container">
                    <div class="title-container text-center mb-2">
                        <h1 class="fw-bold">Service Report Details</h1>
                    </div>
                    <div class="create-report">
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <h5 class="input-group-text">SR-A:</h5>
                                    <h5 class="form-control">{{ form.service_id }}</h5>
                                </div>
                                <span v-if="form.errors.service_id" class="error-message">
                                    {{ form.errors.service_id }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="dateStarted" class="form-label">Date Started:</label>
                                <h5 class="form-control">{{ form.date_started }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="timeStarted" class="form-label">Time Started:</label>
                                <h5 class="form-control">{{ form.time_started }}</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column">
                                    <label for="ticketNumber" class="form-label">Ticket Number:</label>
                                    <h5 class="form-control">{{ form.ticket_number }}</h5>
                                    <span v-if="form.errors.ticket_number" class="error-message">
                                        {{ form.errors.ticket_number }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="technician" class="form-label">Technicians:</label>
                                <h5 class="form-control">{{ form.technician }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="requestingOffice" class="form-label">Requesting Office:</label>
                                <h5 class="form-control">{{ form.requesting_office }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="equipment_no" class="form-label">Equipment, Property Tag/Serial No.:</label>
                                <h5 class="form-control">{{ form.equipment_no }}</h5>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="problemEncountered" class="form-label">Problem Encountered:</label>
                                <h5 class="form-control">{{ form.issue }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="action" class="form-label">Action Taken:</label>
                                <h5 class="form-control">{{ form.action }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="recommendation" class="form-label">Recommendation:</label>
                                <h5 class="form-control">{{ form.recommendation }}</h5>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="dateDone" class="form-label">Date Done:</label>
                                <h5 class="form-control">{{ form.date_done }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="timeDone" class="form-label">Time Done:</label>
                                <h5 class="form-control">{{ form.time_done }}</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="remarks" class="form-label">Remarks</label>
                                <h5 class="form-control">{{ form.remarks }}</h5>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a class="btn btn-outline-secondary" href="/observer/reports/service-report"
                                    @click="setActiveLink('tickets')">Back</a>
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
import Header from '@/Pages/Layouts/ObserverHeader.vue';
import { useForm } from '@inertiajs/vue3';
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

const ticketDetails = (ticket) => {
    if (ticket) {
        selectedTicket = null;
        form.ticket_number = ticket.ticket_number;
        selectedTicket = ticket;
    }
}

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
