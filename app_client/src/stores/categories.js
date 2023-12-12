import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from "./user.js"

export const useCategoriesStore = defineStore('categories', () => {
    const serverBaseUrl = inject('serverBaseUrl')
    const categories = ref(null)
    const userStore = useUserStore()

    const totalCategories = computed(() => {
        return categories.value.length
    })
    
    async function loadCategories() {
        try {
            
            if(userStore.userType == "V"){
                const response = await axios.get('vcards/'+userStore.userId+'/categories')
                categories.value = response.data.data
                console.log(categories.value)
                return categories.value
            }
            const response = await axios.get('categories')
            categories.value = response.data.data
            return categories.value
        } catch (error) {
            clearCategories()
            throw error
        }
    }

    function clearCategories() {
        categories.value = []
    }

    async function insertCategory(newCategory) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the insertProject
        const response = await axios.post('categories', newCategory)
        categories.value.push(response.data.data)
        return response.data.data
    }

    async function updateCategory(updateCategory) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the updateProject
        const response = await axios.put('projects/' + updateCategory.id, updateCategory)
        let idx = categories.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            categories.value[idx] = response.data.data
        }
        return response.data.data
    }

    async function deleteCategory( deleteCategory) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the deleteProject
        const response = await axios.delete('projects/' + deleteCategory.id)
        let idx = categories.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            categories.value.splice(idx, 1)
        }
        return response.data.data
    }  

    return { 
        categories, 
        totalCategories, 
        loadCategories, 
        clearCategories, 
        insertCategory, 
        updateCategory, 
        deleteCategory
    }

})