<script setup>
import { ref, inject } from "vue";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from 'primevue/api';
import InputText from 'primevue/inputtext';
import 'primeicons/primeicons.css'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  administrators: {
    type: Array,
    default: () => [],
  },
  showId: {
    type: Boolean,
    default: true,
  },
  showEmail: {
    type: Boolean,
    default: true,
  },
  showName: {
    type: Boolean,
    default: true,
  },

  showDeleteButton: {
    type: Boolean,
    default: true,
  },
 
});

const emit = defineEmits([ "delete"]);


const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});


const deleteClick = (administrator) => {
  emit("delete", administrator);
};
</script>
<!-- 
<template>

  

  <table class="table">
    <thead>
      <tr>
        <th v-if="showId" class="align-middle">#</th>
        <th v-if="showName" class="align-middle">Name</th>
        <th v-if="showEmail" class="align-middle">Email</th>
        <th v-if="showDeleteButton" class="align-middle"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="administrator in administrators" :key="administrator.id">
        <td v-if="showId" class="align-middle">{{ administrator.id }}</td>
        <td v-if="showName" class="align-middle">{{ administrator.name }}</td>
        <td v-if="showEmail" class="align-middle">{{  administrator.email }}</td>

        <td class="text-end align-middle" v-if="showDeleteButton">
          <div class="d-flex justify-content-end">
            <button
              class="btn btn-xs btn-light"
              @click="deleteClick(administrator)"
              v-if="showDeleteButton"
            >
              <i class="bi bi-xs bi-trash"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template> -->

<template>
  <DataTable v-model:filters="filters" :value="administrators" sortField="id" sortOrder="1"  paginator :rows="10" stripedRows 
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
      <Column  v-if="showId" sortable field="id" header="Id"></Column>
      <Column v-if="showName" sortable field="name" header="Name"></Column>
      <Column v-if="showEmail" sortable field="email" header="E-mail"></Column>
      <Column v-if="showDeleteButton" header="Delete">
          <template #body="slotProps">
              <button
                class="btn btn-xs btn-danger"
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
