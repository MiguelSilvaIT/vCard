<script setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { useUserStore } from "/src/stores/user.js";

const userStore = useUserStore()

const balanceValue = ref(0);
const transactions = ref([]);

const loadBalance = async () => {
  try {
    const response = await axios.get('vcards/'+userStore.userId)
    balanceValue.value = response.data.data.balance
  } catch (error) {
    console.log(error)
  }
}

const loadTransactions = async () => {
  try {
    transactions.value = await userStore.getTransactions();
    console.log(transactions.value)
  } catch (error) {
    console.log(error)
  }
}

const balance = computed(() => balanceValue.value);





onMounted(() => {
  loadBalance()
  loadTransactions()
})
</script>

<template>
  <div class="container">
      <div class="row">
          <div class="col-md-6">
              <div class="card mb-4 mt-4 bg-light">
                  <div class="lead fw-bold m-3 ">
                      Saldo vCard
                  </div>
                  <div class="bg-light">
                      <!-- Aqui você pode inserir o saldo do usuário -->
                      <h5 class="card-title text-success ms-3">{{ balance }} EUR</h5>
                      <p class="card-text ms-3 mb-3">Disponível</p>
                  </div>
              </div>
              <div class="card">
                <div class=" lead fw-bold m-3 ">
                    Atividade recente
                </div>
                <div class="card-body">
                  <div class="transaction bg-light p-2 mb-2" v-for="(transaction, index) in transactions.slice(-6)" :key="index">
                    <div class="transaction-info d-flex justify-content-between">
                      <div>
                        <p class="card-text m-1 fw-bold fs-6">{{ transaction.payment_reference }}</p>
                        <p class="card-text ms-1">{{ new Date(transaction.datetime).toLocaleDateString() }}</p>
                      </div>
                      <p class="card-text m-2 fw-bold fs-5">{{ transaction.type === 'D' ? '-' + transaction.value + ' €' : '+' + transaction.value + ' €' }}</p>
                    </div>
                  </div>
                  <router-link to="#" class="btn btn-dark mt-3">Mostrar tudo</router-link>
                </div>
            </div>
          </div>
          <div class="col-md-6">
              <div class="card mt-4 bg-light">
                  <div class="card-body">
                      <button class="btn btn-primary fs-5 mb-2 rounded-pill w-50 p-1 lead fw-bold">Enviar</button>
                      <!-- Aqui você pode adicionar mais opções -->
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<style scoped>
.transaction {
  border-radius: 5px;
}
</style>
