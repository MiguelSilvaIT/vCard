<script setup>
import { ref,inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Card from 'primevue/card';
import 'primeicons/primeicons.css'
import { FilterMatchMode } from 'primevue/api';
import InputText from 'primevue/inputtext';
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import InputNumber from "primevue/inputnumber";

const serverBaseUrl = inject("serverBaseUrl");
const toast = useToast();
const props = defineProps({
  vcards: {
    type: Array,
    default: () => [],
  },
  showPhoto: {
    type: Boolean,
    default: true,
  },
  showPhoneNumber: {
    type: Boolean,
    default: true,
  },
  showName: {
    type: Boolean,
    default: true,
  },
  showBalance: {
    type: Boolean,
    default: true,
  },
  showActions: {
    type: Boolean,
    default: true,
  },
  showCreateTransactionButton: {
    type: Boolean,
    default: true,
  },
  showUpdateMaxDebitButton: {
    type: Boolean,
    default: true,
  },
  showBlockButton: {
    type: Boolean,
    default: true,
  },
  showDeleteButton: {
    type: Boolean,
    default: true,
  } 
});
const showDialog = ref(false);
const editingVcard = ref();
const emit = defineEmits(["delete", "editMaxDebit", "block"]);
const newMax_debit = ref(null);
const errors = ref(null);

const photoFullUrl = (photo_url) => {
  return photo_url ? serverBaseUrl + "/storage/fotos/" + photo_url
    : avatarNoneUrl;
};

const deleteClick = (vcard) => {
  emit("delete", vcard);
};

const editMaxDebitClick = (vcard,visible) => {
  showDialog.value = visible
  editingVcard.value = vcard
  // emit("editMaxDebit", vcard);
};

const changeMaxDebit = () => {
  console.log("Editing max ",newMax_debit.value)
  if(!newMax_debit.value){
    errors.value = {max_debit: ["Max debit is required."]}
    return
  }
  editingVcard.value.max_debit = newMax_debit.value
  emit("editMaxDebit", editingVcard.value);
  showDialog.value = false
  newMax_debit.value = null
};

const hideDialog = () => {
  showDialog.value = false
  errors.value = null
};

const blockClick = (vcard) => {
  emit("block", vcard);
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat("pt-PT", {
    style: "currency",
    currency: "EUR",
  }).format(value);
};

const selectedVcard = ref();

const onRowExpand = (event) => {
  toast.add({ severity: 'info', summary: 'Vcard Expanded', detail: event.data.name, life: 3000 });
};

const onRowCollapse = (event) => {
  toast.add({ severity: 'info', summary: 'Vcard Collapsed', detail: event.data.name, life: 3000 });
};

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

</script>

<template>
  <DataTable v-model:expandedRows="selectedVcard" @rowSelect="onRowExpand" @rowUnselect="onRowCollapse"
     v-model:selection="selectedVcard" :value="vcards" 
     selectionMode="multiple" dataKey="phone_number"
     :metaKeySelection=false paginator sortField="phone_number" :sortOrder="1" :rows="30" stripedRows 
     v-model:filters="filters" :globalFilterFields="['phone_number','name', 'email','blocked']">
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
      <Column v-if="showPhoto" header="Photo">
        <template #body="slotProps">
            <img :src="photoFullUrl(slotProps.data.photo_url)" :alt="slotProps.data.image" class="align-items-center rounded-circle z-depth-0 avatar-img" />
        </template>
      </Column>
      <Column v-if="showPhoneNumber" sortable  field="phone_number" header="Phone Number"></Column>
      <Column v-if="showName" sortable field="name" header="Name"></Column>
      <Column v-if="showBalance" sortable field="balance" header="Account Balance">
        <template #body="slotProps">
          <div>
            {{ formatCurrency(slotProps.data.balance) }}
          </div>
        </template>
      </Column>
      <Column v-if="showActions" header="Actions">
        <template #body="slotProps">
          <button
              class="btn btn-xs btn-light"
              @click="editMaxDebitClick(slotProps.data, true)"
              v-if="showUpdateMaxDebitButton"
            >
              <i class="bi bi-xs bi-pencil"></i>
          </button>
          <button
              class="btn btn-xs btn btn-dark"
              @click="blockClick(slotProps.data)"
              v-if="showBlockButton"
            >
              <i v-if="slotProps.data.blocked" class="pi pi-unlock"></i>
              <i v-if="!slotProps.data.blocked" class="pi pi-lock"></i>
          </button>
          <button
              class="btn btn-xs btn-danger"
              @click="deleteClick(slotProps.data)"
              v-if="showDeleteButton"
            >
              <i class="pi pi-trash"></i>
          </button>
        </template>
      </Column>
        <template #expansion="slotProps" >
          <div class="expandedDiv">
            <h5 class="ms-3">Vcard # {{ slotProps.data.phone_number }}</h5>
            <div class="contentContainer">
              <div class="ms-1 mt-3">
                <p >
                    <strong>Phone Number:</strong> {{ slotProps.data.phone_number }}
                </p>
                <p >
                    <strong>Name:</strong> {{ slotProps.data.name }}
                </p>
                <p>
                  <strong>E-mail</strong> {{ slotProps.data.email }}
                </p>
              </div>
              <div class="mt-3">
                <p >
                    <strong>Balance:</strong>  {{ formatCurrency(slotProps.data.balance) }}
                </p>
                <p >
                    <strong>Max Debit:</strong> {{ formatCurrency(slotProps.data.max_debit) }}
                </p>
                <p >
                    <strong>Blocked:</strong> {{ slotProps.data.blocked ? 'Yes' : 'No' }}
                </p>
              </div>
              <div class="me-3">
                <p >
                  <img :src="photoFullUrl(slotProps.data.photo_url)" :alt="slotProps.data.image"  />
                </p>
              </div>
            </div>
          </div>
        </template>
    </DataTable>
    <Dialog header="Change Max Debit" v-model:visible="showDialog" :modal="true" :closable="false">
      <div class="ms-4 mb-4">
        <p><strong>Current Max Debit:</strong> {{ formatCurrency(editingVcard.max_debit) }}</p>
      </div>
      <span class="p-float-label">
        <InputNumber v-model="newMax_debit" required inputId="currency-germany" mode="currency" currency="EUR" locale="pt-PT" 
                       :class="{ 'p-invalid': errors ? errors['max_debit'] : false }"/>
        <label for="number-input">Change Max Debit</label>
          <field-error-message :errors="errors" fieldName="max_debit"></field-error-message>
      </span>
      <div class="p-d-flex p-jc-end p-mt-3 mt-4">
        <Button label="Confirm" class="m-3" @click="changeMaxDebit" />
        <Button label="Cancel" class="p-button-secondary m-3" @click="hideDialog" />
      </div>
    </Dialog>
  <Toast />
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

.expandedDiv {
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  min-height: 100px;
  background-color: white;
  margin: 5px;
  border-radius: 4px;
  border-bottom: 1px solid darkgray;
  padding: 10px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08), 0 6px 6px rgba(0, 0, 0, 0.1);
}
.contentContainer{
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  
  /* align-items: center; */
  /* background-color: red; */
}
</style>
