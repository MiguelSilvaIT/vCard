<script setup>
import { ref,inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Card from 'primevue/card';
import 'primeicons/primeicons.css'

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

const emit = defineEmits(["delete", "saveTransaction", "editMaxDebit", "block"]);

const photoFullUrl = (photo_url) => {
  return photo_url ? serverBaseUrl + "/storage/fotos/" + photo_url
    : avatarNoneUrl;
};

const deleteClick = (vcard) => {
  emit("delete", vcard);
};

const saveTransactionClick = (vcard) => {
  emit("saveTransaction", vcard);
};

const exitMaxDebitClick = (vcard) => {
  emit("editMaxDebit", vcard);
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
</script>

<template>
  <DataTable v-model:expandedRows="selectedVcard" @rowSelect="onRowExpand" @rowUnselect="onRowCollapse"
     v-model:selection="selectedVcard" :value="vcards" 
     selectionMode="multiple" dataKey="phone_number"
     :metaKeySelection=false paginator sortField="id" :sortOrder="1" :rows="30" stripedRows >
      <Column v-if="showPhoto" header="Photo">
        <template #body="slotProps">
            <img :src="photoFullUrl(slotProps.data.photo_url)" :alt="slotProps.data.image" class="align-items-center rounded-circle z-depth-0 avatar-img" />
        </template>
      </Column>
      <Column v-if="showPhoneNumber" field="phone_number" header="Phone Number"></Column>
      <Column v-if="showName" field="name" header="Name"></Column>
      <Column v-if="showBalance" field="balance" header="Account Balance">
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
              @click="saveTransactionClick(slotProps.data)"
              v-if="showCreateTransactionButton"
            >
              <i class="pi pi-send"></i>
          </button>
          <button
              class="btn btn-xs btn-light"
              @click="editMaxDebitClick(slotProps.data)"
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
