<template>
    <div class="justify-content-center">
      <div class="d-flex align-items-center justify-content-center" style="padding-left: 200px;">
        <!-- Login Section -->
        <div class="login-section">
          <h2 class="fw-semibold">Login</h2>
          <form @submit.prevent="login">
            <label for="username">Username:</label>
            <input v-model="username" type="text" id="username" required />
  
            <label for="password">Password:</label>
            <input v-model="password" type="password" id="password" required />
  
            <button type="submit" class="rounded primary btnm">Login</button>
            <button type="button" @click="forgotPassword" class="rounded btnn secondary">Forgot Password</button>
          </form>
        </div>
  
        <!-- Recent Tickets Section -->
        <div class="d-flex flex-row gap-2">
          <!-- ... (existing recent tickets code) -->
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import Header from "@/Pages/Layouts/AdminHeader.vue";
  import {Link, router} from "@inertiajs/vue3"
  </script>
  
  <style>
  
  .login-section {
    display: flex;
    flex-direction: column;
    width: 300px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    margin-bottom: 20px;
  }
  
  .login-section form {
    display: flex;
    flex-direction: column;
  }
  
  .login-section label {
    margin-top: 10px;
  }
  
  .login-section input {
    margin-bottom: 10px;
    padding: 8px;
  }
  
  .login-section button {
    margin-top: 10px;
    padding: 10px;
    cursor: pointer;
  }
  </style>
  