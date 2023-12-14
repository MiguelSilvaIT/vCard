<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import AdministratorTable from "./AdministratorTable.vue"
import { useToast } from "vue-toastification"


import { useUserStore } from "/src/stores/user.js"


const toast = useToast()

const userStore = useUserStore()

const router = useRouter()

const administrators = ref([])


const loadAdministrators = async () => {
  try {
      const response = await axios.get('admins')
      administrators.value = response.data.data
  } catch (error) {
    administrators.value = []
    console.log(error)
  }
}



const deleteAdministrator = (administrator) => {
  try {
      const response =  axios.delete(`admins/${administrator.id}`)
      console.log(response)
      toast.success('Administrator #' + administrator.id + ' was deleted successfully.')
      router.back()

    } catch (error) {
      
      console.log(error)
      toast.error('Administrator was not deleted!')      
    }
}

const addAdministrator = () => {
  router.push({ name: 'NewAdministrator' })
}

onMounted(() => {
  loadAdministrators()
})
</script>

<template>
  <h3 class="mt-5 mb-3">Administrators</h3>
  <hr>
  <div class="mx-2 mt-2 mb-4 d-flex justify-content-between">
    <button type="button" class="btn btn-success px-4 btn-addprj" @click="addAdministrator">
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp;
      Add Administrator
    </button>

  </div>
  <administrator-table 
  :administrators="administrators" 
  :showId="false" 
  @delete="deleteAdministrator">
  </administrator-table>
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

