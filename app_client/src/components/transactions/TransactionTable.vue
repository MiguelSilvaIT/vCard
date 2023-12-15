<script setup>
import { ref, inject } from "vue";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
// import { useToast } from "vue-toastification";
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import Card from 'primevue/card';
import {useCategoriesStore} from '/src/stores/categories.js';

const toast = useToast();
const serverBaseUrl = inject("serverBaseUrl");
const categoriesStore = useCategoriesStore();

const props = defineProps({
  transactions: {
    type: Array,
    default: () => [],
  },
  showVcard: {
    type: Boolean,
    default: false,
  },
  showPaymentType:{
    type: Boolean,
    default: true,
  },
  showPaymentReference:{
    type: Boolean,
    default: true,
  },
  showType: {
    type: Boolean,
    default: true,
  },
  showValue: {
    type: Boolean,
    default: true,
  },
  showDate: {
    type: Boolean,
    default: true,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
 
});

const emit = defineEmits(["edit"]);

const editClick = (transaction) => {
  emit("edit", transaction);
};


const stockClass = (type) => {
  if( type === 'D'){
    return [
      'ml-1 text-danger'
    ];
  }
  return [
    'text-success'
  ];
};

const formatCurrency = (value,type) => {
  const format = new Intl.NumberFormat("pt-PT", {
    style: "currency",
    currency: "EUR",
  }).format(value);
  if(type === 'D'){
    return ` -${format}`;
  }
  else{
    return  ` +${format}` ;
  }
  
};

console.log(formatCurrency(100, 'D')); // Outputs: "- €100.00"
console.log(formatCurrency(100, 'C'));

const selectedTransaction = ref();

const onRowExpand = (event) => {
  toast.add({ severity: 'info', summary: 'Transaction Expanded', detail: event.data.name, life: 3000 });
};
const onRowCollapse = (event) => {
  console.log("here1");
  toast.add({ severity: 'info', summary: 'Transaction Collapsed', detail: event.data.name, life: 3000 });
};

const transactionCategory = (id) => {
  console.log("id");
  categoriesStore.loadCategory(id);
  return categoriesStore.categoryName;
};
</script>

<template>
  <DataTable v-model:expandedRows="selectedTransaction" @rowSelect="onRowExpand" @rowUnselect="onRowCollapse"
     v-model:selection="selectedTransaction" :value="transactions" 
     selectionMode="multiple" dataKey="id"
     :metaKeySelection=false paginator sortField="date" :sortOrder="-1" :rows="30" stripedRows >
      <Column v-if="showVcard" field="vcard" header="Vcard"></Column>
      <Column v-if="showPaymentType" field="payment_type" header="Payment Type"></Column>
      <Column v-if="showPaymentReference" field="payment_reference" header="Payment Reference"></Column>
      <Column v-if="showType" field="type" header="Type"></Column>
      <Column v-if="showValue" field="value" sortable header=" Value">
        <template #body="slotProps">
          <div :class="stockClass(slotProps.data.type)">
            {{ formatCurrency(slotProps.data.value, slotProps.data.type) }}
          </div>
        </template></Column>
      <Column v-if="showDate" field="date" sortable header="Date"></Column>
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
        <template #expansion="slotProps">
          <Card>
            <template #title> Transaction #{{ slotProps.data.id }}</template>
            <template #content>
                <p >
                    <strong>Payment Type:</strong> {{ slotProps.data.payment_type }}
                </p>
                <p >
                    <strong>Payment Reference:</strong> {{ slotProps.data.payment_reference }}
                </p>
                <p  >
                    <strong>Payment Value:</strong>  
                    <span :class="stockClass(slotProps.data.type)">
                      {{ formatCurrency(slotProps.data.value, slotProps.data.type) }}
                    </span>
                </p>
                <p >
                    <strong>Old Balance:</strong> {{ slotProps.data.old_balance }}
                </p>
                <p >
                    <strong>New Balance:</strong> {{ slotProps.data.new_balance }}
                </p>
                <p >
                    <strong>Date:</strong> {{ slotProps.data.date }}
                </p>
                <p >
                    <strong>Type:</strong> {{ slotProps.data.type == 'D' ? 'Debit' : 'Credit'  }}
                </p>
                <p  v-if="slotProps.data.description">
                    <strong>Description:</strong> {{ slotProps.data.description }}
                </p>
                <p >
                    <strong>Sent from:</strong> {{ slotProps.data.vcard }}
                </p>
                <p v-if="slotProps.data.category_id">
                    <strong>Category:</strong> {{transactionCategory(slotProps.data.category_id)}}
                </p>
            </template>
        </Card>
        </template>
    </DataTable>
  <Toast />
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}
</style>