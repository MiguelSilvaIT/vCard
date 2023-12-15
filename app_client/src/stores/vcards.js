import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useVcardsStore = defineStore('vcards', () => {

    const serverBaseUrl = inject('serverBaseUrl')
    const vcards = ref(null)

    async function loadVcards() {
        try {
            const response = await axios.get('vcards')
            vcards.value = response.data.data
            // console.log(vcards.value)
        } catch (error) {
            throw error
        }
    }

    return { vcards, loadVcards}
})