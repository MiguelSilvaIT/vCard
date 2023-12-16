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

const confirmation_codes = ref({
    confirmation_code: '',
    oldconfirmation_code: ''    
})

const errors = ref(null)


const changeConfirmationCode = async() => {
  console.log("CHange",confirmation_codes.value)
  try {
    const response = await axios.patch(`vcards/${userStore.userId}/confirmation_code`, confirmation_codes.value);
    console.log("Response",response)      
    
    toast.success('Confirmation code has been changed.')
    router.push({ name: 'Dashboard' })
  } catch (error) {
    if(error.response.status == 422)
      errors.value = error.response.data.errors
    toast.error('Error changing confirmation code!')
  }
}
</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="changeConfirmationCode">
    <h3 class="mt-5 mb-3">Change Confirmation Code</h3>
    <hr>
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-5">
          <span class="p-float-label">
            <InputText type="password" v-model="confirmation_codes.oldconfirmation_code" 
                    :class="{ 'is-invalid': errors ? errors['oldconfirmation_code'] : false }"/>
            <label for="number-input">Old Confirmation Code</label>
            <field-error-message :errors="errors" fieldName="oldconfirmation_code"></field-error-message>
          </span>
        </div>
     
        <div class="mb-5">
          <span class="p-float-label">
            <InputText type="password" v-model="confirmation_codes.confirmation_code" 
                    :class="{ 'is-invalid': errors ? errors['confirmation_code'] : false }"/>
            <label for="number-input">New Confirmation Code</label>
            <field-error-message :errors="errors" fieldName="confirmation_code"></field-error-message>
          </span>
        </div>
    <div class="mb-3 d-flex justify-content-center">
      <button type="button" class="btn btn-primary px-5" @click="changeConfirmationCode">Change Confirmation Code</button>
    </div>
      </div>
    </div>
  </form>
</template>

