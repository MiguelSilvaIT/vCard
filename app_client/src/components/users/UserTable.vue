<script setup>
import { ref, inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'
import { useUserStore } from "../../stores/user.js"

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from 'primevue/api';
import InputText from 'primevue/inputtext';
import 'primeicons/primeicons.css'


const serverBaseUrl = inject("serverBaseUrl");
const userStore = useUserStore()

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
  showEmail: {
    type: Boolean,
    default: true,
  },
  showName: {
    type: Boolean,
    default: true,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
  showDeleteButton: {
    type: Boolean,
    default: true,
  },
});

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const emit = defineEmits(["edit","delete"]);

const editClick = (user) => {
  emit("edit", user);
};

const deleteClick = (user) => {
  emit("delete", user);
};
</script>

<template>
  <DataTable v-model:filters="filters" :value="users" removableSort  paginator :rows="10" stripedRows 
    :globalFilterFields="['name', 'email']">
      <template #header>
          <div class="flex justify-content-end">
              <span class="p-input-icon-left">
                  <i class="pi pi-search" />
                  <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
              </span>
          </div>
      </template>
      <template #empty> No customers found. </template>
      <template #loading> Loading customers data. Please wait. </template>
      <Column v-if="showName" sortable field="name" header="Name"></Column>
      <Column v-if="showEmail" sortable field="email" header="E-mail"></Column>
      <Column v-if="showEditButton" header="Edit">
          <template #body="slotProps">
              <button
                class="btn btn-xs btn-light"
                @click="editClick(slotProps.data)"
                v-if="showEditButton"
              >
                <i class="bi bi-xs bi-pencil"></i>
              </button>
          </template>
          <i class="pi pi-trash"></i>
      </Column>
      <Column v-if="showDeleteButton" header="Delete">
          <template #body="slotProps">
              <button
                class="btn btn-xs btn-light"
                @click="deleteClick(slotProps.data)"
                v-if="showDeleteButton"
              >
              <i class="pi pi-trash"></i>
              </button>
          </template>
      </Column>
    </DataTable>
</template>


<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

.img_photo {
  width: 3.2rem;
  height: 3.2rem;
}
</style>
