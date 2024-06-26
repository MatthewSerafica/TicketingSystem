<template>
  <div>
    <Header class="sticky-top" style="z-index: 110;"></Header>
    <div class="position-absolute end-0">
      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showSuccessToast"
        :success="page.props.flash.success" :message="page.props.flash.message" @close="handleClose">
      </Toast>

      <Toast
        x-data="{ shown: false, timeout: null, resetTimeout: function() { clearTimeout(this.timeout); this.timeout = setTimeout(() => { this.shown = false; $dispatch('close'); }, 5000); } }"
        x-init="resetTimeout; shown = true;" x-show.transition.opacity.out.duration.5000ms="shown" v-if="showErrorToast"
        :error="page.props.flash.error" :error_message="page.props.flash.error_message" @close="handleClose">
      </Toast>
    </div>
    <div class="d-flex justify-content-center flex-column align-content-center align-items-center">
      <div class="text-center justify-content-center align-items-center d-flex mt-5 flex-column">
        <div class="d-flex flex-column justify-content-center align-items-center gap-2">
          <h1 class="fw-bold">View All Offices</h1>
          <p class="fs-5">Manage All Offices</p>
          <Link :href="route('admin.office.create')">
            <Button :name="'Add New Office'" :color="'primary'" class="btn btn-tickets btn-primary py-2 px-5"></Button>
          </Link>
        </div>
        <div class="input-group mt-3">
          <span class="input-group-text" id="searchIcon"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control py-2" id="search" name="search" v-model="search"
            placeholder="Search Offices..." aria-label="searchIcon" aria-describedby="searchIcon" />
        </div>
      </div>

      <div class="w-75 mt-2">
        <div v-if="offices.data.length" class="d-flex justify-content-start justify-content-md-end mb-2">
          <div class="d-flex flex-column mt-3 mt-md-0">
          <pagination :links="offices.links" :key="'offices'" />
          </div>
        </div>
        <div class="table-responsive rounded shadow pt-2 px-2 mb-3 overflow-auto">
          <table class="table table-hover custom-rounded-table">
            <thead>
              <tr class="text-start">
                <th class="text-center text-muted">Office ID</th>
                <th class="text-muted" style="width: 40%;">Offices</th>
                <th class="text-center text-muted">Date Created</th>
                <th class="text-center text-muted">Date Updated</th>
                <th class="text-muted">Delete Option</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="office in offices.data" class="align-middle">
                <td class="text-center">{{ office.id }}</td>
                <td style="max-width: 60px;" @dblclick="startEditing(office.id, office.office)">
                  <span v-if="selectedOfficeId !== office.id">{{ office.office }}</span>
                  <input v-model="editedOffice[office.id]" v-if="selectedOfficeId === office.id"
                    @keyup.enter="saveOffice(office.id)" @blur="saveOffice(office.id)">
                </td>
                <td class="text-center">{{ formatDate(office.created_at) }}</td>
                <td class="text-center">{{ formatDate(office.updated_at) }}</td>
                <td><button type="button" as="button" class="btn btn-danger" @click="showDelete(office)">Delete</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <Delete v-if="isShowDelete" :office="selectedOfficeId" @closeDelete="closeDelete"/>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue';
import Delete from "@/Components/DeleteModal.vue";
import pagination from "@/Components/Pagination.vue";
import Toast from '@/Components/Toast.vue';
import Header from "@/Pages/Layouts/AdminHeader.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import Alpine from 'alpinejs';
import moment from "moment";
import { reactive, ref, watch, watchEffect } from 'vue';

Alpine.start()

const page = usePage();

let showSuccessToast = ref(false);
let showErrorToast = ref(false);

watchEffect(() => {
  showSuccessToast.value = !!page.props.flash.success;
  showErrorToast.value = !!page.props.flash.error;
})

const handleClose = () => {
  page.props.flash.success = null;
  page.props.flash.error = null;
  showSuccessToast.value = false;
  showErrorToast.value = false;
}

const props = defineProps({
  offices: Object,
  filters: Object,
});


const formatDate = (date) => {
  return moment(date, 'YYYY-MM-DD').format('MMM DD, YYYY');
};

let search = ref(props.filters.search);
let sortColumn = ref("id");
let sortDirection = ref("asc");
let timeoutId = null;
let editedOffice = reactive({});
let selectedOfficeId = ref(null);

const fetchData = () => {
  router.get(
    route('admin.office'),
    {
      search: search.value,
      sort: sortColumn.value,
      direction: sortDirection.value,
    },
    {
      preserveState: true,
      replace: true,
    }
  )

}
const resetSorting = () => {
  console.log("Reset Sorting");
  sortColumn.value = "id"
  sortDirection.value = "asc"
}

const debouncedFetchData = () => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
  timeoutId = setTimeout(() => {
    fetchData()
  }, 500)
}

watch(search, () => {
  if (!search.value) {
    resetSorting()
  }
  debouncedFetchData();
})

const startEditing = (officeId, officeName) => {
  selectedOfficeId.value = officeId;
  if (!editedOffice[officeId]) {
    editedOffice[officeId] = officeName;
  }
};

// Method to save the edited office name
const saveOffice = (officeId) => {
  if (officeId && editedOffice[officeId] !== null) {
    const form = useForm({
      office: editedOffice[officeId],
    });
    form.put(route('admin.office.update', { office_id: officeId }));
    selectedOfficeId.value = null;
  }
};

const isShowDelete = ref(false);

function closeDelete() {
  if(isShowDelete.value) {
    selectedOfficeId.value = null
    isShowDelete.value = false;
  }
}

function showDelete(office) {
  if(!isShowDelete.value) {
    selectedOfficeId.value = office;
    isShowDelete.value = true;
  }
}

</script>

<style scoped>

.table-responsive {
  overflow-x: auto; 
}

.btn-tickets {
  transition: all 0.2s;
}

.btn-tickets:hover {
  transform: scale(1.1);
}

.btn-options {
  width: 100px;
}

.custom-rounded-table {
  border-radius: 10px;
}


.ticket-description {
  position: relative;
  cursor: default;
}

.ticket-description:hover::after {
  content: attr(data-hover-text);
  position: absolute;
  top: 100%;
  left: 0;
  background-color: pink;
  color: red;
  padding: 5px;
  border-radius: 5px;
  z-index: 9999;
}

</style>