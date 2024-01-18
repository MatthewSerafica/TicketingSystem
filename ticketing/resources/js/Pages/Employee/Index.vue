<template>
    <Header></Header>
    <br>

    <div class="search">
        <input
        type="text"
        v-model="searchQuery"
        placeholder="Search Tickets..."
        @input="handleSearch"
      />
    </div>

    <div class="container text-center w-100 h-100 justify-center">
        <H1>View All Tickets</H1>
        <p> Manage and Track all TMDD tickets</p>
        <button>Create New ticket</button>
        <br><br>
        <button>All</button>
        <button>New</button>
        <button>Pending</button>
        <button>Resolved</button>
    </div>
    <div class="table-container">

    </div>
</template>
<script setup>
import Header from "@/Pages/Layouts/Header.vue"
</script>


<style>
* {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
</style>

