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

const administrator = ref(newAdministrator())


const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''




const save = () => {

  console.log("administrator.value -->" , administrator.value)
  axios.post('admins', administrator.value)
    .then((response) => {
      toast.success('Administrator Created')
      console.dir(response.data.data)
      router.back()

    })
    .catch((error) => {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error("Validation Error")
      }
    })
}

const cancel = () => {
  originalValueStr = JSON.stringify(administrator.value)
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
    //confirmationLeaveDialog.value.show()
  } else {
    // No value has changed, so we can leave the component without confirming
    next()
  }
})




</script>

<template>
  <!-- <confirmation-dialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </confirmation-dialog>   -->

  <administrator-detail  :administrator="administrator" :errors="errors" @save="save"
    @cancel="cancel" ></administrator-detail>
</template>
