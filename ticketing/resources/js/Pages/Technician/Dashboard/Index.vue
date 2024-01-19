<template>
    <div>
      <Header></Header>
      <div class="recent">
        <div class="d-flex">
          <div class="recent-tickets">
            <h2 class="fw-semibold">Recent Tickets</h2>
            <div class="d-flex gap-2">
              <Link class="text-decoration-none" href="/technician/tickets/create">
                <Button class="rounded btnn secondary" value="Create Ticket"></Button>
              </Link>
              <Link class="text-decoration-none" href="/technician/tickets">
                <Button class="rounded primary btnm text-white" value="View All"></Button>
              </Link>
            </div>
          </div>
          <div class="cards">
            <Card
              :no="one.no"
              :issue="one.issue"
              :employee="one.employee"
              :department="one.department"
              :status="one.status"
              :technician="one.technician"
              :date="one.date"
            ></Card>
            <Card
              :no="two.no"
              :issue="two.issue"
              :employee="two.employee"
              :department="two.department"
              :status="two.status"
              :technician="two.technician"
              :date="two.date"
            ></Card>
            <Card
              :no="three.no"
              :issue="three.issue"
              :employee="three.employee"
              :department="three.department"
              :status="three.status"
              :technician="three.technician"
              :date="three.date"
            ></Card>
          </div>
        </div>
        {{ props.ticket }}
        <br>
        <br>
        {{ props.technician }}
      </div>
    </div>
  </template>

<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Cards.vue';
import Header from '@/Pages/Layouts/TechnicianHeader.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    ticket: Object,
    technician: Object,
})

const one = {
    no: 1901,
    issue: "Network Issue",
    employee: "John Doe",
    department: "Finance",
    status: "Ongoing",
    technician: "John Smith",
    date: "16/01/2024"
}
const two = {
    no: 1902,
    issue: "Software Installation",
    employee: "Jane Doe",
    department: "Help Desk",
    status: "Pending",
    technician: "John Smith",
    date: "16/01/2024"
}
const three = {
    no: 1903,
    issue: "Printer Problem",
    employee: "Jane Smith",
    department: "Registrar",
    status: "New",
    technician: "John Smith",
    date: "16/01/2024"
}

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

.primary {
    background-color: #000066;
}

.btnm {
    transition: background-color 0.3s, color 0.3s;
}

.btnm:hover {
    background-color: #00009c;
    color: #CC9900;
}

.secondary {
    background-color: #efefef;
}

.btnn {
    transition: background-color 0.3s, color 0.3s;
}

.btnn:hover {
    background-color: #ffffff;
    color: #000000;
}</style>