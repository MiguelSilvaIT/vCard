<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import CategoryTable from "./CategoryTable.vue"

const router = useRouter()

const categories = ref([])

const totalCategories = computed(() => {
  return categories.value.length
})

const loadCategories = async () => {
    try {
      const response = await axios.get('categories/900000001')
    categories.value = response.data.data

  } catch (error) {
    console.log(error)
  }
}

const editCategory = (category) => {
  router.push({ name: 'Category', params: { id: category.id } })
}

const addCategory = () => {
  router.push({name: 'NewCategory'})
  }

onMounted (() => {
  loadCategories()
})
</script>

<template>
  <h3 class="mt-5 mb-3">Categories</h3>
  <hr>
  <div class="mx-2 mt-2">
      <button
        type="button"
        class="btn btn-success px-4 btn-addprj"
        @click="addCategory">
        <i class="bi bi-xs bi-plus-circle"></i>&nbsp; Add Category</button>
  </div>
  <category-table
    :categories="categories"
    :showId="false"
    @edit="editCategory"
  ></category-table>
</template>

<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 2.3rem;
}
</style>

