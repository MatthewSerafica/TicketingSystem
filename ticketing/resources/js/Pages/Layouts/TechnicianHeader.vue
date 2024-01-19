<template>
    <nav class="navbar navbar-expand-lg shadow-sm header-color">
        <div class="container-fluid gap-3">
            <div class="d-flex gap-2 col-8">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-brilliance mt-2" viewBox="0 0 16 16">
                    <path
                        d="M8 16A8 8 0 1 1 8 0a8 8 0 0 1 0 16M1 8a7 7 0 0 0 7 7 3.5 3.5 0 1 0 0-7 3.5 3.5 0 1 1 0-7 7 7 0 0 0-7 7" />
                </svg>
                <a class="navbar-brand text-white" href="#">TMDD Ticketing System</a>
            </div>
            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button> -->
            <div class="" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active text-white" aria-current="page" href="/technician">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/technician/tickets">Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Service Reports</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex gap-2 col-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                    class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                </svg>
                <a class="nav-link" href="#">Technician</a>
            </div>
        </div>
    </nav>
</template>

<script setup>
</script>

<style>
.header-color {
    background-color: #063970;
}
</style>