<script setup>
import { ref, inject } from "vue";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from 'primevue/api';
import InputText from 'primevue/inputtext';
import 'primeicons/primeicons.css'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
  showId: {
    type: Boolean,
    default: true,
  },
  showType: {
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

const emit = defineEmits(["edit", "delete"]);

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const deleteClick = (category) => {
  emit("delete", category);
};

const editClick = (category) => {
  emit("edit", category);
};
</script>

<template>
  <DataTable v-model:filters="filters" sortField="id" :sortOrder="1" :value="categories"  paginator :rows="10" stripedRows 
    :globalFilterFields="['id','name', 'type']">
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
      <Column v-if="showId" sortable field="id" header="Id"></Column>
      <Column v-if="showName" sortable field="name" header="Name"></Column>
      <Column v-if="showType" sortable field="type" header="Type"></Column>
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
      </Column>
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
