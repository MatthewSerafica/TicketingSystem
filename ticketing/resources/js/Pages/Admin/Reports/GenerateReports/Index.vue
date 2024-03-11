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
        </div>
        <!--CTAs and Search-->
        <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
            <div class="d-flex flex-column justify-content-center align-items-center gap-2">
                <h1 class="fw-bold">Generate Reports</h1>
            </div>

            <div class="table-responsive rounded shadow pt-2 px-2 w-75">
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
                            <tr class="align-middle">
                                <td class="text-start">1</td>
                                <td class="text-start fw-bold">February 2024</td>
                                <td class="text-center d-flex gap-2 justify-content-center align-items-center">
                                    <Button :name="'Download'" :color="'primary'"></Button>
                                    <Button :name="'Print'" :color="'outline-primary'"></Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Pagination from '@/Components/Pagination.vue';
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import moment from "moment";
import { nextTick, reactive, ref, watch, watchEffect } from "vue";
</script>