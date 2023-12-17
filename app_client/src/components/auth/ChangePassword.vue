<script setup>
import { useToast } from "vue-toastification"
import { useRouter } from 'vue-router'
import { useUserStore } from '../../stores/user.js'
import { ref } from 'vue'
import InputText from 'primevue/inputtext'
import axios from 'axios'

const toast = useToast()
const router = useRouter()
const userStore = useUserStore()

const passwords = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const errors = ref(null)


const changePassword = async () => {
  try {
    if(userStore.userType == 'A'){
      await axios.patch(`admins/${userStore.userId}/updatePassword`, passwords.value);
      toast.success('Password has been changed.')
      router.push({ name:'Reports'})
    } else {
      await axios.patch(`vcards/${userStore.userId}/updatePassword`, passwords.value);
      toast.success('Password has been changed.')
      router.push({ name: 'Dashboard'})
    }
  } catch (error) {
    if (error.response.status == 422) 
      errors.value = error.response.data.errors
      toast.error('Error changing password!')
  }
}
</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="changePassword">
    <h3 class="mt-5 mb-3">Change Password</h3>
    <hr>
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-5" >
          <span class="p-float-label">
            <InputText type="password" v-model="passwords.current_password" 
                    :class="{ 'is-invalid': errors ? errors['current_password'] : false }"/>
            <label for="number-input">Current Password</label>
            <field-error-message :errors="errors" fieldName="current_password"></field-error-message>
          </span>
        </div>

        <div class="mb-5">
          <span class="p-float-label">
            <InputText type="password" v-model="passwords.password" 
                    :class="{ 'is-invalid': errors ? errors['password'] : false }"/>
            <label for="number-input">Password</label>
            <field-error-message :errors="errors" fieldName="password"></field-error-message>
          </span>
        </div>
     
        <div class="mb-5">
          <span class="p-float-label">
            <InputText type="password" v-model="passwords.password_confirmation" 
                    :class="{ 'is-invalid': errors ? errors['password_confirmation'] : false }"/>
            <label for="number-input">Password Confirmation</label>
            <field-error-message :errors="errors" fieldName="password_confirmation"></field-error-message>
          </span>
        </div>

        <div class="mb-3 d-flex justify-content-center">
          <button type="button" class="btn btn-primary px-5" @click="changePassword">Change Password</button>
        </div>
      </div>
    </div>
  </form>
</template>

