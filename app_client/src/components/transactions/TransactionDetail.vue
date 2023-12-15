<script setup>
import { ref, watch, computed, inject } from "vue";

import { useUserStore } from "/src/stores/user.js"
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';
import { useCategoriesStore } from '../../stores/categories'

const categoriesStore = useCategoriesStore()
const userStore = useUserStore()

const props = defineProps({
  transaction: {
    type: Object,
    required: true,
  },
  operationType: {
      type: String,
      default: 'insert'  // insert / update
  },
  errors: {
      type: Object,
      required: false,
    },
});
console.log("Props",props)


const emit = defineEmits(["save", "cancel"]);

const editingTransaction = ref(props.transaction)
const category = ref(null)

watch(
  () => props.transaction,
  (newTransaction) => {
    editingTransaction.value = newTransaction
    if(props.transaction.type != null)
      categoriesStore.loadCategories(props.transaction.type)
    console.log("Editing Transaction",editingTransaction.value.type)
  },
  { immediate: true }
)

const payment_type = [
  
  { value: 'VCARD'},
  { value: 'MBWAY' },
  { value: 'PAYPAL' },
  { value: 'IBAN'},
  { value: 'VISA' },
  { value: 'MB' }
];



const transactionTittle = computed( () => {
    if (!editingTransaction.value) {
      return ''
    }
    //   console.log("Transaction",editingTransaction.value.data)
    return props.operationType == 'insert' ? 'New Transaction' : 'Transaction #' + editingTransaction.value.id
})

const operation = computed( () => props.operationType == 'insert' ? 'insert' : 'update')

// console.log("Operation",operation.value)
const save = () => {
  console.log("Editing Transaction",editingTransaction.value)
  emit("save", editingTransaction.value);
}

const cancel = () => {
  emit("cancel", editingTransaction.value);
}
</script>

<template>
  <form class="row g-3 needs-validation" v-if = "operation == 'insert'" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-2">{{transactionTittle}}</h3>
    <hr />
    <div class="d-flex flex-wrap mt-4 justify-content-between">
      <div class="w-75 pe-4">
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
                          :class="{ 'p-invalid': errors ? errors['payment_type'] : false }"/>
            <label for="dd-paymentType">Select Payment Type</label>
            <field-error-message :errors="errors" fieldName="payment_type"></field-error-message>
          </div>
        </div>
        <div class="col mb-5 ms-xs-3">
          <div class="p-float-label">
            <InputText type="text" v-model="editingTransaction.payment_ref" :class="{ 'p-invalid': errors ? errors['payment_ref'] : false }"/>
            <label for="dd-paymentType">Payment Reference</label>
            <field-error-message :errors="errors" fieldName="payment_ref"></field-error-message>
          </div>
        </div>     
        <div class="mb-5">
          <span class="p-float-label">
            <InputText type="text" v-model="editingTransaction.description" />
            <label for="number-input">Description</label>
          </span>
        </div>
        <div class="mb-5 ">
          <span class="p-float-label">
            <Dropdown v-model="editingTransaction.category_id" :options="categoriesStore.categories" optionLabel="name" optionValue="id"/>
            <label for="number-input">Category</label>
          </span>
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>

  <form class="row g-3 needs-validation" v-if = "operation != 'insert'" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">{{transactionTittle}}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-4">
          <span class="p-float-label">
            <InputText type="text" v-model="transaction.description" />
            <label for="number-input">Description</label>
          </span>
        </div>
        <div class="mb-4 ">
          <span class="p-float-label">
            <Dropdown v-model="transaction.category_id" :options="categoriesStore.categories" optionLabel="name" optionValue="id"/>
            <label for="number-input">Category</label>
          </span>
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
