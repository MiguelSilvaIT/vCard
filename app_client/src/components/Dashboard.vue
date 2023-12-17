<script setup>
import axios from 'axios';
import { computed, onMounted, ref, inject } from 'vue';
import { useUserStore } from "/src/stores/user.js";
import { useToast } from "vue-toastification";
import { useRouter } from 'vue-router'

const userStore = useUserStore()
const socket = inject('socket')
const toast = useToast()
const router = useRouter()

const balanceValue = ref(0);
const piggyBalanceValue = ref(0);
const totalBalanceValue = ref(0);
const transactions = ref([]);
const amount = ref('');
const errors = ref(null);
const balance = computed(() => balanceValue.value);
const piggyBalance = computed(() => piggyBalanceValue.value);
const totalBalance = computed(() => totalBalanceValue.value);

const loadBalance = async () => {
  try {
    const response = await axios.get('vcards/' + userStore.userId)
    balanceValue.value = response.data.data?.balance ?? 0
    piggyBalanceValue.value = response.data.data.custom_data?.value ?? 0
    totalBalanceValue.value = parseFloat(balanceValue.value) + parseFloat(piggyBalanceValue.value)
  } catch (error) {
    console.log(error)
  }
}

const loadTransactions = async () => {
  try {
    transactions.value = await userStore.getTransactions();
  } catch (error) {
    console.log(error)
  }
}

socket.on('newTransaction', (novatransaction) => {
  toast.info(`Recebeu ${novatransaction.value}€ de ${novatransaction.vcard}!`)
  loadTransactions()
  loadBalance()
})

const UpdatePiggy = async (value, currentAction) => {

  const requestData = {
    value: parseFloat(value.replace(',', '.')),
    action: currentAction,
  };

  try {
    const response = await axios.patch('vcards/' + userStore.userId + '/piggybank', requestData);
    balanceValue.value = response.data.data.balance;
    piggyBalanceValue.value = response.data.data.custom_data.value;
    toast.success("Saldo atualizado com sucesso");
    errors.value = null;
    return response.data;
  } catch (error) {
    if (error.response.status == 422) {
      console.log(error);
        errors.value = error.response.data.errors
        toast.error("Erro na quantidade introduzida")
      }
  }
};

const addTransaction = () => {
  router.push({ name: 'NewTransaction' })
}

const showTransactions = () => {
  router.push({ name: 'Transactions' })
}

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
            <h5 class="card-title text-success ms-3">{{ parseFloat(totalBalance).toFixed(2) }} EUR</h5>
            <p class="card-text ms-3 mb-3">Disponível</p>
          </div>
        </div>
        <div class="card">
          <div class=" lead fw-bold m-3 ">
            Atividade recente
          </div>
          <div class="card-body">
            <div class="transaction bg-light p-2 mb-2" v-for="(transaction, index) in transactions.slice(-6)"
              :key="index">
              <div class="transaction-info d-flex justify-content-between">
                <div>
                  <p class="card-text m-1 fw-bold fs-6">{{ transaction.payment_reference }}</p>
                  <p class="card-text ms-1">{{ new Date(transaction.datetime).toLocaleDateString() }}</p>
                </div>
                <p class="card-text m-2 fw-bold fs-5">{{ transaction.type === 'D' ? '-' + transaction.value + ' €' : '+' +
                  transaction.value + ' €' }}</p>
              </div>
            </div>
            <router-link to="#" class="btn btn-dark mt-3" @click="showTransactions" >Mostrar tudo</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card mt-4 bg-light">
          <div class="card-body">
            <button class="btn btn-dark fs-6 mb-2 rounded-pill w-50 p-1 lead fw-bold" @click ="addTransaction">Enviar</button>
            <!-- Aqui você pode adicionar mais opções -->
          </div>
        </div>
        <div class="card mb-4 mt-4 bg-light">
          <div class="lead fw-bold m-3">
            Saldo Piggy Bank
          </div>
          <div class="bg-light">
            <!-- Aqui você pode inserir o saldo do usuário -->
            <h5 class="card-title text-success ms-3">{{ parseFloat(piggyBalance).toFixed(2) }} EUR</h5>
            <p class="card-text ms-3 mb-3">Guardado</p>
          </div>
          <div class="lead fw-bold m-3">
            Saldo Disponível
          </div>
          <div class="bg-light">
            <!-- Aqui você pode inserir o saldo do usuário -->
            <h5 class="card-title text-success ms-3">{{ parseFloat(balance).toFixed(2) }} EUR</h5>
            <p class="card-text ms-3 mb-3">Saldo Disponível para transações</p>
          </div>
          <field-error-message :errors="errors" fieldName="value" class="align-self-center"></field-error-message>
          <form novalidate @submit.prevent="UpdatePiggy" class="forms">
              <label for="numberInput" class="margin_right">Quantidade</label>
              <input v-model="amount" 
                      type="text" 
                      id="numberInput" 
                      name="numberInput" 
                      class="card" 
                      :class="{ 'p-invalid': errors ? errors['value'] : false }"
                      required>
            </form>
          <div class="col">
            <div class="card-body d-flex justify-content-between">
              <br>
              <br>
                <button class="btn btn-success fs-6 mb-2 rounded-pill w-50 p-1 lead fw-bold"
                  style="background-color: green; border-color: green;" @click="UpdatePiggy(amount, 'save')">Depositar</button>
                <button class="btn btn-success fs-6 mb-2 rounded-pill w-50 p-1 lead fw-bold"
                  style="background-color: red; border-color: red;" @click="UpdatePiggy(amount, 'withdraw')">Levantar</button>
            </div>
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
.margin_right {
  margin-right: 10px;
}
.forms {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
