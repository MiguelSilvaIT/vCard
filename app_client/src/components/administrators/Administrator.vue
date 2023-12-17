<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch, computed } from 'vue'
import AdministratorDetail from "./AdministratorDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "/src/stores/user.js"

const toast = useToast()
const router = useRouter()

const userStore = useUserStore()

const props = defineProps({
  id: {
    type: Number,
    default: null
  }
})

const newAdministrator = () => {
  return {
    id: null,
    name: '',
    email: '',
  }
}

const operation = computed( () => (!props.id || props.id < 0) ? 'insert' : 'update')

const loadAdministrator = async (id) => {
  if (!id || (id < 0)) {
    administrator.value = newAdministrator()
    originalValueStr = JSON.stringify(administrator.value)
  } else {
      try {
        const response = await axios.get(`admins/${id}`)
        administrator.value = response.data.data
        originalValueStr = JSON.stringify(administrator.value)
      } catch (error) {
        console.log(error)
      }
  }
}

watch(
  () => props.id,
  (newValue) => {
      loadAdministrator(newValue)
    },
  {immediate: true}  
)

const administrator = ref(newAdministrator())

const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)

let originalValueStr = JSON.stringify(administrator.value)

const save = () => {
  console.log("administrator.value -->" , administrator.value)
  if (operation.value == 'insert') 
  {
  axios.post('admins', administrator.value)
    .then((response) => {
      toast.success('Administrator Created')
      console.dir(response.data.data)
      originalValueStr = JSON.stringify(administrator.value)
      router.push({ name: 'Administrators' })
    })
    .catch((error) => {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error("Validation Error")
      }
    })
  } else {
    axios.put(`admins/${administrator.value.id}`, administrator.value)
      .then((response) => {
        toast.success('Administrator Updated')
        console.dir(response.data.data)
        originalValueStr = JSON.stringify(administrator.value)
        router.push({ name: 'Administrators' })
      })
      .catch((error) => {
        if (error.response && error.response.status == 422) {
          errors.value = error.response.data.errors
          toast.error("Validation Error")
        }
      })
  }
}

const cancel = () => {
  router.back()
}


let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = JSON.stringify(administrator.value)
  if (originalValueStr != newValueStr) {
    // Some value has changed - only leave after confirmation
    nextCallBack = next
    confirmationLeaveDialog.value.show()
  } else {
    // No value has changed, so we can leave the component without confirming
    next()
  }
})
</script>

<template>
  <confirmation-dialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </confirmation-dialog>  

  <administrator-detail  :administrator="administrator" :operationType="operation" :errors="errors" @save="save"
    @cancel="cancel" ></administrator-detail>
</template>
