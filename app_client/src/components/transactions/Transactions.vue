<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import TransactionTable from "./TransactionTable.vue"

import { useUserStore } from "/src/stores/user.js"

const userStore = useUserStore()

const router = useRouter()

const transactions = ref([])


const onChange = () => {
  //console.log("Valor select box--> " + filterByOwner.value)
  loadCategories()
}



const totalTransactions = computed(() => {
  return transactions.value.length
})

const loadTransactions = async () => {
  try {
    if (userStore.userType == "V") {
      const response = await axios.post('vcards/'+userStore.userId+'/transactions')
      console.log(response.data)
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
})
</script>

<template>
  <h3 class="mt-5 mb-3">Transactions</h3>
  <hr>
  <div class="mx-2 mt-2 d-flex justify-content-between">
    <button type="button" class="btn btn-success px-4 btn-addprj" @click="addTransaction">
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp;
      Add new transaction
    </button>
  </div>
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
</style>

