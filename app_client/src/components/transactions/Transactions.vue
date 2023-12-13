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
  console.log("Filtros",filters.value);
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
      console.log(filters.value);
      transactions.value = response.data
    } else { 
      const response = await axios.get('transactions')
      console.log(response.data.data)
      transactions.value = response.data.data
    }
  } catch (error) {
    console.log(error)
  }
}

const editTransaction = (transaction) => {
  router.push({ name: 'Transaction', params: { id: transaction.id } })
}

const addTransaction = () => {
  router.push({ name: 'NewTransaction' })
}

onMounted(() => {
  loadTransactions()
  const startDate = new Date()
  const endDate = new Date(new Date().setDate(startDate.getDate() + 7))
  date.value = [startDate, endDate];
})

const handleDate = (modelData) => {
  console.log("ModelData",modelData);
  filters.value.filter_start_date = new Date(modelData[0]).toISOString().split('T')[0];
  if(modelData[1] == null){
    filters.value.filter_end_date = null;
  }
  else{
    filters.value.filter_end_date = new Date(modelData[1]).toISOString().split('T')[0];
  }
    console.log("Filtros",filters.value);
  // do something else with the data
}

</script>

<template>
  <h3 class="mt-5 mb-3">Transactions</h3>
  <hr>
  <div class="mx-2 mt-2 mb-5 d-flex justify-content-between">
    <button type="button" class="btn btn-success px-4 btn-addprj" @click="addTransaction">
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp;
      Add new transaction
    </button>
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
            :manualInput="false" model-type="yyyy-MM-dd" @update:modelValue="handleDate" :maxDate="today"/>
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