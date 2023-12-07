import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'

export const useUserStore = defineStore('user', () => {
    const serverBaseUrl = inject('serverBaseUrl')
    const user = ref(null)
    const userName = computed(() => user.value?.name ?? 'Anonymous')
    //const phoneNumber = computed(() => user.value.phoneNumber) 
    const userId = computed(() => user.value?.id ?? -1)

    const userType  = computed(() => user.value?.user_type ?? "N")
    

    const userPhotoUrl = computed(() =>
        user.value?.photo_url ?
         serverBaseUrl + '/storage/fotos/' + user.value.photo_url:
         avatarNoneUrl)
    async function loadUser() {
        try {
            const response = await axios.get('users/me')
            user.value = response.data.data
            console.log(user.value.user_type)
        } catch (error) {
            clearUser()
            throw error
        }
    }
    function clearUser () {
        delete axios.defaults.headers.common.Authorization
        sessionStorage.removeItem('token')
        user.value = null
    }

    async function login(credentials) {
        try {
            const response = await axios.post('/auth/login', credentials)
            console.log(response)
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            sessionStorage.setItem('token', response.data.access_token)
            await loadUser()
            return true
        }
        catch(error) {
            clearUser()
            return false
        }
    }

    async function logout () {
        try {
            await axios.post('logout')
            clearUser()
            return true
        } catch (error) {
            return false
        }
    }

    async function restoreToken () {
        let storedToken = sessionStorage.getItem('token')
        if (storedToken) {
        axios.defaults.headers.common.Authorization = "Bearer " + storedToken
        await loadUser()
        return true
        }
        clearUser()
        return false
        }

    return { user, userName, userId, userPhotoUrl,userType, loadUser, clearUser, login, logout,restoreToken}
})