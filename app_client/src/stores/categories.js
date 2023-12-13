import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from "./user.js"

export const useCategoriesStore = defineStore('categories', () => {
    const serverBaseUrl = inject('serverBaseUrl')
    const categories = ref(null)
    const categoryName = ref(null)
    const userStore = useUserStore()

    const totalCategories = computed(() => {
        return categories.value.length
    })

    async function loadCategory(category_id) {
        try {
            if(userStore.userType == "V"){
                const response = await axios.get('categories/'+category_id)
                categoryName.value = response.data.data.name
                console.log(categoryName.value)
            }
        } catch (error) {
            throw error
        }
    }

    async function loadCategories(transaction_type) {
        try {
            if(userStore.userType == "V"){
                try{
                    const response = await axios.get('vcards/'+userStore.userId+'/categories', {params: {'transaction_type': transaction_type}})
                    categories.value = response.data.data
                }
                catch(error){
                    clearCategories()
                    throw error
                }
            }
            const response = await axios.get('default/categories', {params: {'transaction_type': transaction_type}})
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
        try{
            const response = await axios.post('categories', newCategory)
            categories.value.push(response.data.data)
        }
        catch(error){
            throw error
        }
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
        const response = await axios.delete('categories/' + deleteCategory.id)
        let idx = categories.value.findIndex((t) => t.id === response.data.data.id)
        if (idx >= 0) {
            categories.value.splice(idx, 1)
        }
        return response.data.data
    }  

    return { 
        categories, 
        categoryName,
        totalCategories, 
        loadCategories, 
        clearCategories, 
        insertCategory, 
        updateCategory, 
        deleteCategory,
        loadCategory
    }
})