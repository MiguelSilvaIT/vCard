<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import CategoryTable from "./CategoryTable.vue"

import { useUserStore } from "/src/stores/user.js"

const userStore = useUserStore()

const router = useRouter()

const categories = ref([])

const filterByOwner = ref(-1)

const onChange = () => {
  //console.log("Valor select box--> " + filterByOwner.value)
  loadCategories()
}



const totalCategories = computed(() => {
  return categories.value.length
})

const loadCategories = async () => {
  try {
    
    if(filterByOwner.value == "my-cat")
    {
      const response = await axios.get('vcards/'+userStore.userId+'/categories')
      categories.value = response.data.data
    }
    else
    {
      const response = await axios.get('categories')
      categories.value = response.data.data
    }

  } catch (error) {
    console.log(error)
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
</script>

<template>
  <h3 class="mt-5 mb-3">Categories</h3>
  <hr>
  <div class="mx-2 mt-2 d-flex justify-content-between">
    <button type="button" class="btn btn-success px-4 btn-addprj" @click="addCategory">
      <i class="bi bi-xs bi-plus-circle"></i>&nbsp;
      Add Category
    </button>
    <select 
    class="form-select ml-auto small-select" 
    aria-label="Default select example"
    v-model="filterByOwner"
    @change="onChange()"
    >
      <option value="my-cat" selected>My Categories</option>
      <option value="all-cat" >Default Categories</option>
   
    </select>
  </div>
  <category-table :categories="categories" :showId="false" @edit="editCategory"></category-table>
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

