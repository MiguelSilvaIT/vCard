<script setup>

  import axios from 'axios'
  import { useToast } from "vue-toastification"
  import { useRouter } from 'vue-router'
  import { ref } from 'vue'
  import { useUserStore } from '/src/stores/user.js'
  import InputText from 'primevue/inputtext'


  const toast = useToast()
  const router = useRouter()
  const credentials = ref({
    username: '',
    password: ''
  })
  const errors = ref(null)

  const userStore = useUserStore()

  const emit = defineEmits(['auth/login'])
  const login = async () => {
     if (await userStore.login(credentials.value)) {
      toast.success('User ' + userStore.user.name + ' has entered the application.')
      emit('auth/login')
      if(userStore.userType === 'A'){
        router.push({ name: 'Administrators' })
      }else{
        router.push({ name: 'Dashboard' })
      }
      
    } else {
      credentials.value.password = ''
    }
  }
</script>
<template>
  <form
    class="row g-3 needs-validation"
    novalidate
    @submit.prevent="login"
  >
  <h3 class="mt-5 mb-3">Login</h3>
    <hr>
  <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-5">
          <span class="p-float-label">
            <InputText type="text" v-model="credentials.username" 
                    :class="{ 'is-invalid': errors ? errors['username'] : false }"/>
            <label for="number-input">Username</label>
            <field-error-message :errors="errors" fieldName="username"></field-error-message>
          </span>
        </div>
     
        <div class="mb-5">
          <span class="p-float-label">
            <InputText type="password" v-model="credentials.password" 
                    :class="{ 'is-invalid': errors ? errors['password'] : false }"/>
            <label for="number-input">Password</label>
            <field-error-message :errors="errors" fieldName="password"></field-error-message>
          </span>
        </div>
    
    <!-- <div class="mb-3">
      <div class="mb-3">
        <label
          for="inputUsername"
          class="form-label"
        >Username</label>
        <input
          type="text"
          class="form-control"
          id="inputUsername"
          required
          v-model="credentials.username"
        >
      </div>
    </div>
    <div class="mb-3">
      <div class="mb-3">
        <label
          for="inputPassword"
          class="form-label"
        >Password</label>
        <input
          type="password"
          class="form-control"
          id="inputPassword"
          required
          v-model="credentials.password"
        > -->
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-center">
      <button
        type="button"
        class="btn btn-primary px-5"
        @click="login"
      >Login</button>
    </div>
  </form>
</template>

