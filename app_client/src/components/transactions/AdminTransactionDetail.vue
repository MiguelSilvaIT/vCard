<script setup>
import { ref, watch, computed, inject, onMounted } from "vue";


import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';

import { useUserStore } from "/src/stores/user.js";
import { useVcardsStore } from '../../stores/vcards';

import AutoComplete from 'primevue/autocomplete';

const userStore = useUserStore()
const vcardsStore = useVcardsStore()

const props = defineProps({
  transaction: {
    type: Object,
    required: true,
  },
  errors: {
      type: Object,
      required: false,
    },
});


const emit = defineEmits(["save", "cancel"]);

onMounted(() => {
  vcardsStore.loadVcards()
})
console.log("Props",props)
const editingTransaction = ref(props.transaction)

watch(
  () => props.transaction,
  (newTransaction) => {
    editingTransaction.value = newTransaction
  },
  { immediate: true }
)


const payment_type = [
  { value: 'MBWAY' },
  { value: 'PAYPAL' },
  { value: 'IBAN'},
  { value: 'VISA' },
  { value: 'MB' }
];

const save = () => {
  emit("save", editingTransaction.value);
}

const cancel = () => {
  emit("cancel", editingTransaction.value);
}

</script>

<template>
    
  <form class="row g-3 mt-1 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">New Transaction</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-5">
          <span class="p-float-label">
            <Dropdown v-model="editingTransaction.vcard" editable :options="vcardsStore.vcards" optionLabel="phone_number" optionValue="phone_number"
                         :class="{ 'p-invalid': errors ? errors['vcard'] : false }"/>
            <label for="number-input">Transaction Vcard</label>
            <field-error-message :errors="errors" fieldName="vcard"></field-error-message>
          </span> 
        </div>
        <div class="col mb-5 ms-xs-3">
          <span class="p-float-label">
            <InputNumber v-model="editingTransaction.value" inputId="currency-germany" mode="currency" currency="EUR" locale="ge-GE" 
                        :class="{ 'p-invalid': errors ? errors['value'] : false }"/>
            <label for="number-input">Transaction Value</label>
            <field-error-message :errors="errors" fieldName="value"></field-error-message>
          </span> 
        </div>
        <div class="col mb-5 ms-xs-3">
          <div class="p-float-label">
            <Dropdown v-model="editingTransaction.payment_type" :options="payment_type" optionLabel="value" optionValue="value" 
                        :class="{ 'p-invalid': errors ? errors['type'] : false }"/>
            <label for="dd-paymentType">Select Payment Type</label>
            <field-error-message :errors="errors" fieldName="type"></field-error-message>
          </div>
        </div>
        <div class="col mb-5 ms-xs-3">
          <div class="p-float-label">
            <InputText type="text" v-model="editingTransaction.payment_ref" :class="{ 'p-invalid': errors ? errors['payment_ref'] : false }"/>
            <label for="dd-paymentType">Payment Reference</label>
            <field-error-message :errors="errors" fieldName="payment_ref"></field-error-message>
          </div>
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
.formLabel {
  font-size: 12px;
    opacity: 0.7;
    margin-bottom: 6px;
    margin-left: 12px;
}
</style>
