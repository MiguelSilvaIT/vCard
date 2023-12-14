<script setup>
import { inject } from "vue";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
// import ColumnGroup from 'primevue/columngroup';   // optional
// import Row from 'primevue/row';


const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  transactions: {
    type: Array,
    default: () => [],
  },
  showVcard: {
    type: Boolean,
    default: true,
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

const formatCurrency = (value) => {
  return  new Intl.NumberFormat("pt-PT", {
    style: "currency",
    currency: "EUR",
  }).format(value);
};
</script>

<template>
  <DataTable :value="transactions" paginator sortField="date" :sortOrder="-1" :rows="30" stripedRows >
      <Column v-if="showVcard" field="vcard" header="Vcard"></Column>
      <Column v-if="showPaymentType" field="payment_type" header="Payment Type"></Column>
      <Column v-if="showPaymentReference" field="payment_reference" header="Payment Reference"></Column>
      <Column v-if="showType" field="type" header="Type"></Column>
      <Column v-if="showValue" field="value" sortable header=" Value">
        <template #body="slotProps">
            {{ formatCurrency(slotProps.data.value) }}
        </template></Column>
      <Column v-if="showDate" field="date" sortable header="Date"></Column>
      <Column v-if="showEditButton" header="Edit" class="text-end">
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
  </DataTable>
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

</style>