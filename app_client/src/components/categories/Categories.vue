<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import CategoryTable from "./CategoryTable.vue"

import { useUserStore } from "/src/stores/user.js"
import { useCategoriesStore } from '../../stores/categories'

const userStore = useUserStore()

const router = useRouter()
const categoriesStore = useCategoriesStore()
const categories = ref([])


const loadCategories = async () => {
  try {
    
    // if(filterByOwner.value == "my-cat")
    // {
    //   const response = await axios.get('vcards/'+userStore.userId+'/categories')
    //   categories.value = response.data.data
    // }
    // else
    // {
    //   const response = await axios.get('categories')
    //   categories.value = response.data.data
    // }

   
      const response = await axios.get(userStore.userType === 'A' ? 'categories/default' : 'categories')
      categories.value = response.data.data


  } catch (error) {
    console.log(error)
  }
}
const deleteCategory = (category) => {
  categoriesStore.deleteCategory(category)
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

</script>

<template>
  <h3 class="mt-5 mb-3">Categories</h3>
  <hr>
  <div class="mx-2 mt-2  mb-5 d-flex justify-content-between">
    <button type="button" class="btn btn-success px-4 btn-addprj" @click="addCategory">
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp;
      Add Category
    </button>
  </div>
  <category-table :categories="categories" @edit="editCategory" @delete="deleteCategory"></category-table>
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

