<template>
  <div>
    <Header></Header>
    <div class="page-container">
      <div class="login-section">
        <h2 class="fw-semibold">Login</h2>
        <form @submit.prevent="login">

          <label for="username">Email:</label>
          <input v-model="username" type="text" id="username" required />

          <label for="password">Password:</label>
          <input v-model="password" type="password" id="password" required />

          <router-link @click="forgotPassword" class="forgot-password-link">Forgot Password</router-link>
          <button type="submit" class="rounded primary btnm">Login</button>
          
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import Header from "@/Pages/Layouts/LoginHeader.vue";
import { Link, router, useForm } from "@inertiajs/vue3";

const form = useForm({
  email: null,
  password: null,
})
</script>

<style scoped>

.page-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

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
  border-radius: 8px;
}

.login-section button {
  margin-top: 10px;
  padding: 10px;
  cursor: pointer;
  color: white; 
}

.forgot-password-link {
  margin-top: 10px;
  text-decoration: underline;
  cursor: pointer;
  color: #000066; 
} 
</style>
