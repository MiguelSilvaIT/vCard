<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useUserStore } from "/src/stores/user.js"
import Dropdown from 'primevue/dropdown';
import Chart from 'primevue/chart';

const userStore = useUserStore()

const errors = ref(null)
const totalExpenses = ref(0)
const totalEarnings = ref(0)
const balanceMin = ref(0)
const balanceMax = ref(0)
const balanceHistory = ref([])
const totalTransactions = ref(0)
const months = ref([])
const selectedMonth = ref(null)

const chartData = ref();
const chartOptions = ref();

onMounted( async () => {
  await loadTotalDebit()
  await loadTotalCredit()
  await loadMonths()
  await loadTransactions()
  await loadBalanceInfo()
  chartData.value = setChartData()
  chartOptions.value = setChartOptions()

})

const onChange = () => {
  loadTotalDebit(selectedMonth.value)
  loadTotalCredit(selectedMonth.value)
  loadTransactions(selectedMonth.value)

}


const loadBalanceInfo = async () => {
  errors.value = null

  try {
    const response = await axios.get('balance/' + userStore.userId)

    balanceMin.value = response.data.minBalance
    balanceMax.value = response.data.maxBalance
    balanceHistory.value = response.data.allBalances

  } catch (error) {
    console.log(error)
  }
}


const setChartData =  () => 
{
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: balanceHistory.value.map(item => item.date),
        datasets: [
            {
                label: 'Balance',
                data: balanceHistory.value.map(item => item.old_balance),
                fill: false,
                borderColor: documentStyle.getPropertyValue('--blue-500'),
                tension: 0.4
            },
        
        ],
        
    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary');
    const surfaceBorder = documentStyle.getPropertyValue('--surface-border');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.4,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
  };


const loadMonths = async () => {

  try {
    const response = await axios.get('months/')

    months.value = response.data

    months.value.unshift({ month: 'Todos' })

  } catch (error) {
    console.log(error)
  }
}


const loadTotalDebit = async (month) => {
  errors.value = null

  if (month && month !== 'Todos') {
    try {
      const response = await axios.get('totalDebit/' + userStore.userId + '/' + month)
      totalExpenses.value = response.data
    } catch (error) {
      console.log(error)
    }
  }
  else {
    try {
      const response = await axios.get('totalDebit/' + userStore.userId)
      totalExpenses.value = response.data
    } catch (error) {
      console.log(error)
    }
  }
}

  const loadTotalCredit = async (month) => {
    errors.value = null
  
    if (month && month !== 'Todos') {
      try {
        const response = await axios.get('totalCredit/' + userStore.userId + '/' + month)
        totalEarnings.value = response.data
      } catch (error) {
        console.log(error)
      }
    }
    else {
      try {
        const response = await axios.get('totalCredit/' + userStore.userId)
        totalEarnings.value = response.data
      } catch (error) {
        console.log(error)
      }
    }
  }

  const loadTransactions = async (month) =>{
    errors.value = null

    if (month && month !== 'Todos') {
      try {
        const response = await axios.get('totalTransactions/' + userStore.userId + '/' + month)
        totalTransactions.value = response.data
      } catch (error) {
        console.log(error)
      }
    }
    else {
      try {
        const response = await axios.get('totalTransactions/' + userStore.userId)
        totalTransactions.value = response.data
      } catch (error) {
        console.log(error)
      }
    }
  }



</script>

<template>
  <div class="statistics-page">
    <div class="dropdown-container">
      <Dropdown v-model="selectedMonth" :options="months" optionLabel="month" optionValue="month" @change="onChange()"
        placeholder="Select a Month" class="w-full md:w-14rem" />
    </div>

    <div class="statistics-container">
      <div class="statistics-card card-blue">
        <div class="card-content">
          <h3>Despesas Totais</h3>
          <div class="value">{{ totalExpenses }}</div>
        </div>
      </div>

      <div class="statistics-card card-green">
        <div class="card-content">
          <h3>Ganhos Totais</h3>
          <div class="value">{{totalEarnings}}</div>
        </div>
      </div>

      <div class="statistics-card card-orange">
        <div class="card-content">
          <h3>Transações Realizadas</h3>
          <div class="value">{{totalTransactions}}</div>
        </div>
      </div>

    </div>
        <Chart type="line" :data="chartData" :options="chartOptions" class="chart-balance" />
    
  </div>
</template>

<style scoped>
.statistics-page {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  height: 200vh;
  margin-top: 50px;
}

.dropdown-container {
  position: relative;
  z-index: 1;
  /* Garante que o dropdown fique acima dos outros elementos */
  margin-bottom: 16px;
}

.statistics-container {
  display: flex;
  flex-wrap: wrap;
}

.statistics-card {
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin: 0 8px 16px 8px;
  overflow: hidden;
  width: 200px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out;
}

.statistics-card:hover {
  transform: scale(1.05);
}

.card-content {
  padding: 16px;
  text-align: center;
}

h3 {
  margin-bottom: 8px;
  font-size: 1.2em;
  color: #fff;
}

.chart-balance {
  width: 100%;
  height: 40%; /* Ocupar toda a largura disponível */
  /* Adicione outras regras de estilo conforme necessário */
}

.value {
  font-size: 1.5em;
  font-weight: bold;
  color: #fff;
}

.card-blue {
  background-color: #3498db;
}

.card-green {
  background-color: #2ecc71;
}

.card-orange {
  background-color: #e67e22;
}
</style>
