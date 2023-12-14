import axios from 'axios'
import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import avatarNoneUrl from '@/assets/avatar-none.png'
import {useToast} from 'vue-toastification'

export const useUserStore = defineStore('user', () => {

    const socket = inject('socket')
    const toast = useToast()

    const serverBaseUrl = inject('serverBaseUrl')
    const user = ref(null)
    const userName = computed(() => user.value?.name ?? 'Anonymous')
    //const phoneNumber = computed(() => user.value.phoneNumber) 
    const userId = computed(() => user.value?.id ?? -1)
    const userType = computed(() => user.value?.user_type ?? "N")
    

    const userPhotoUrl = computed(() =>
        user.value?.photo_url ?
         serverBaseUrl + '/storage/fotos/' + user.value.photo_url:
         avatarNoneUrl)
         
    async function loadUser() {
        try {
            const response = await axios.get('users/me')
            user.value = response.data.data
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
            axios.defaults.headers.common.Authorization = "Bearer " + response.data.access_token
            await loadUser()
            if(user.value.blocked ){
              toast.error(`A sua conta foi bloqueada por um administrador.`)
              clearUser()
              return false
            }
            sessionStorage.setItem('token', response.data.access_token)
            socket.emit('loggedIn', user.value)
            return true
        }
        catch(error) {
            clearUser()
            toast.error('User credentials are invalid!')
            return false
        }
    }

    async function changePassword(credentials) {
        if (userId.value < 0) {
          throw "Anonymous users cannot change the password!";
        }
        try {
          await axios.patch(`users/${user.value.id}/password`, credentials);
          return true;
        } catch (error) {
          throw error;
        }
      }

    async function logout () {
        try {
            await axios.post('logout')
            socket.emit('loggedOut', user.value)
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
        socket.emit('loggedIn', user.value)
        return true
        }
        clearUser()
        return false
    }

    async function getTransactions() {
        try {
          const response = await axios.post('vcards/' + userId.value + '/transactions', {
            userId: userId.value
          });
          const sortedTransactions = response.data.sort((a, b) => new Date(b.datetime) - new Date(a.datetime));
          return sortedTransactions.slice(0, 6);
        } catch (error) {
          console.error('Failed to load transactions:', error);
          return [];
        }
    }   

    socket.on('blockedUser', (user) => {
      console.log("user bloqueado", user)
      toast.error(`A sua conta foi bloqueada por um administrador.`)
      logout()
    })

    return { user, userName, userId, userPhotoUrl, userType, loadUser, clearUser, login, logout,restoreToken, getTransactions, changePassword}
})