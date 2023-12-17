<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { useUserStore } from "/src/stores/user.js"
import Dropdown from 'primevue/dropdown';
import Chart from 'primevue/chart';

const userStore = useUserStore()

const errors = ref(null)
const qntVcards = ref(-1)
const qntBlockedVcards = ref(-1)
const allVcardsBalanceSum = ref(-1)
const qntVcardsCreated = ref(-1)
const months = ref([])

const chartData = ref();
const chartOptions = ref();

onMounted(async () => {

  await loadBalanceSum()
  await loadVcardsCount()
  await loadBlockedVcardsCount()
  await loadMonths()
  await loadCreatedVcardsHistory()
  chartData.value = setChartData()
  chartOptions.value = setChartOptions()

})




const loadCreatedVcardsHistory = async () => {
  errors.value = null

  try {
    const response = await axios.get('stats/vcards/count/created')

    qntVcardsCreated.value = response.data

  } catch (error) {
    console.log(error)
  }
}

const loadBlockedVcardsCount = async () => {
  errors.value = null

  try {
    const response = await axios.get('stats/vcards/count/blocked')
    console.log('response.data.data -->', response)

    qntBlockedVcards.value = response.data

  } catch (error) {
    console.log(error)
  }
}


const loadBalanceSum = async () => {
  errors.value = null

  try {
    const response = await axios.get('balance/sum')

    allVcardsBalanceSum.value = response.data

  } catch (error) {
    console.log(error)
  }
}

const loadVcardsCount = async () => {
  errors.value = null

  try {
    const response = await axios.get('stats/vcards/count')
    console.log('response.data.data -->', response)

    qntVcards.value = response.data

  } catch (error) {
    console.log(error)
  }
}


const setChartData = () => {
  const documentStyle = getComputedStyle(document.documentElement);

  return {
    labels: months.value.map(item => item.month),
    datasets: [
      {
        label: 'New Vcards',
        data: qntVcardsCreated.value.map(item => item.total),
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


    //console.log('months.value -->', months.value[0].month)
  } catch (error) {
    console.log(error)
  }
}









</script>

<template>
  <div class="statistics-page">
    <div class="statistics-container">
      <div class="statistics-card card-blue">
        <div class="card-content">
          <h3>Quantidade de Vcards</h3>
          <div class="value">{{ qntVcards }}</div>
        </div>
      </div>

      <div class="statistics-card card-green">
        <div class="card-content">
          <h3>Balanço Total</h3>
          <div class="value">{{ allVcardsBalanceSum }}</div>
        </div>
      </div>

      <div class="statistics-card card-orange">
        <div class="card-content">
          <h3>Vcards Bloqueados</h3>
          <div class="value">{{ qntBlockedVcards }}</div>
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
  height: 40%;
  /* Ocupar toda a largura disponível */
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
