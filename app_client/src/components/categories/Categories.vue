<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import CategoryTable from "./CategoryTable.vue"
import { useToast } from "vue-toastification"

import { useUserStore } from "/src/stores/user.js"
import Button from 'primevue/button'

const userStore = useUserStore()

const router = useRouter()
const categories = ref([])
const toast = useToast()

const endpoint = userStore.userType === 'A' ? 'default_categories' : 'vcards/'+userStore.userId+'/categories';

const loadCategories= async () => {
    try{
        const response = await axios.get(`${endpoint}`)
        categories.value = response.data.data
    }
    catch(error){
        clearCategories()
        throw error
    }
}

const deleteCategory = (category) => {
  try {
    if(userStore.userType == 'V')
      axios.delete(`categories/${category.id}`)
    else{
      axios.delete(`default_categories/${category.id}`)
    }
      let idx = categories.value.findIndex((t) => t.id === category.id)
      if (idx >= 0) {
          categories.value.splice(idx, 1)
      }
      toast.success('Category #' + category.id + ' was deleted successfully.')
    } catch (error) {
      console.log(error)
      toast.error('Category was not deleted!')      
    }
}

const editCategory = (category) => {
  router.push({ name: 'Category', params: { id: category.id } })
}

const addCategory = () => {
  router.push({ name: 'NewCategory' })
}

onMounted(() => {
  loadCategories()
})
const showId = computed(() => userStore.userType === 'A')
</script>

<template>
  <h3 class="mt-5 mb-3">Categories</h3>
  <hr>
  <div class="mx-2 mt-2  mb-4 d-flex justify-content-between">
    <Button type="button" class="border-round-xs"  @click="addCategory">
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp;
      Add Category
    </Button>
  </div>
  <category-table :categories="categories" :show-id="showId" @edit="editCategory" @delete="deleteCategory"></category-table>
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

