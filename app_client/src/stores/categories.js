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
                // console.log(categoryName.value)
            }
        } catch (error) {
            throw error
        }
    }

    async function loadCategories(transaction_type) {
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
        else{
            try{
                const response = await axios.get('default_categories', {params: {'transaction_type': transaction_type}})
                categories.value = response.data.data
            }
            catch(error){
                clearCategories()
                throw error
            }
        }
    }

    function clearCategories() {
        categories.value = []
    }
    return { 
        categories, 
        categoryName,
        totalCategories, 
        loadCategories, 
        clearCategories, 
        loadCategory
    }
})