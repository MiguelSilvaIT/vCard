<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import TransactionTable from "./TransactionTable.vue"

import { useUserStore } from "/src/stores/user.js"

import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';

const userStore = useUserStore()

const router = useRouter()

const transactions = ref([])

const filters = ref({
  filterByValue: 'value_asc',
  filterByType: 'T',
  filter_start_date: null,
  filter_end_date:null,
  filterByPaymentType: null
});

const date = ref(null);

const today = ref(new Date());


const payment_type = [
  { value: ''},
  { value: 'VCARD'},
  { value: 'MBWAY' },
  { value: 'PAYPAL' },
  { value: 'IBAN'},
  { value: 'VISA' },
  { value: 'MB' }
];

const type = [
  { value: 'T', name: 'Credit And Debit' },
  { value: 'D', name: 'Debit' },
  { value: 'C', name: 'Credit' },
];


const loadTransactions = async () => {
  try {
    if (userStore.userType == "V") {
      const response = await axios.post('vcards/'+userStore.userId+'/transactions', 
      {
        'filter_by_value':filters.value.filterByValue,
        'filter_by_type':filters.value.filterByType,
        'filter_start_date':filters.value.filter_start_date,
        'filter_end_date':filters.value.filter_end_date,
        'filter_payment_type':filters.value.filterByPaymentType,
      })
      transactions.value = response.data
    } else { 
      const response = await axios.get('transactions')
      transactions.value = response.data.data
    }
  } catch (error) {
    console.log(error)
    clearTransactions()
  }
}

const clearTransactions = () => {
  transactions.value = []
}

const editTransaction = (transaction) => {
  router.push({ name: 'Transaction', params: { id: transaction.id } })
}

const addTransaction = () => {
  router.push({ name: 'NewTransaction' })
}

onMounted(() => {
  loadTransactions()
  date.value = null;
})

const handleDate = (modelData) => {
  if(modelData == null){
    filters.value.filter_start_date = null;
    filters.value.filter_end_date = null;
    return;
  }
  filters.value.filter_start_date = new Date(Date.UTC(modelData[0].getFullYear(), modelData[0].getMonth(), modelData[0].getDate())).toISOString().split('T')[0];
  if(modelData[1] == null){
    filters.value.filter_end_date = null;
  }
  else{
    filters.value.filter_end_date = new Date(Date.UTC(modelData[1].getFullYear(), modelData[1].getMonth(), modelData[1].getDate())).toISOString().split('T')[0];
  }
}

</script>

<template>
  <h3 class="mt-5 mb-3">Transactions</h3>
  <hr>
  <div class="mx-2 mt-2 mb-5 d-flex justify-content-between">
    <Button type="button" @click="addTransaction">
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp;
      Add new transaction
    </Button>
  </div>
  <form class="col  needs-validation" @submit.prevent="loadTransactions">
    <div class="row g-3">
      <div class="col mb-3 ms-xs-3">
        <span class="p-float-label">
          <Dropdown v-model="filters.filterByPaymentType" :options="payment_type" optionLabel="value" optionValue="value"/>
          <label for="birth_date">Transaction Type</label>
        </span>
      </div>
      <div class="col mb-3 ms-xs-3">
        <span class="p-float-label">
          <Dropdown v-model="filters.filterByType" :options="type" optionLabel="name" optionValue="value"/>
          <label for="birth_date">Transaction Type</label>
        </span>
      </div>
      <div class="col mb-3 ms-xs-3">
        <span class="p-float-label">
          <Calendar v-model="date" selectionMode="range" 
            :manualInput="false" model-type="yyyy-MM-dd" @update:modelValue="handleDate" :maxDate="today" showButtonBar />
          <label for="birth_date">Select Dates</label>
        </span>
      </div>
    </div>
    <div class="g-3 mb-4 d-flex justify-content-end ">
        <Button label="Filter" type="submit" class="mr-5" ></Button>
    </div>
  </form>
  <transaction-table :transactions="transactions" :showId="false" @edit="editTransaction"></transaction-table>
</template>

<style scoped>
  .filter-div {
    min-width: 12rem;
  }

  .total-filtro {
    margin-top: 2.3rem;
  }

  .small-select {
    width: 200px; /* Ajuste este valor conforme necessário */
    
  }

  .filterButton{
    margin-bottom: 2.3rem;
    margin-right: 2.3rem; 
    position: absolute;
  }
</style>