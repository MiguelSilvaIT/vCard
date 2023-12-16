<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch , inject} from 'vue'
import { useUserStore} from '../../stores/user.js'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import VcardDetail from "./VcardDetail.vue"


const toast = useToast()
const router = useRouter()
const socket = inject('socket')

const props = defineProps({
    phone_number: {
      type: Number,
      default: null
    }
})
const userStore = useUserStore()

const newVcard = () => {
    return {
      name: '',
      email: '',
      photo_url: null,
      phone_number: '',
      password: '',
      password_confirmation: '',
      confirmation_code: '',
    }
}

const vcard = ref(newVcard())


const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const loadVcard = async (phone) => {
  originalValueStr = ''
  errors.value = null
  if (!phone || (phone < 0)) {
    vcard.value = newVcard()
    console.log("aqui" ,vcard.value)
  } else {
      try {
        const response = await axios.get('/vcards/' + phone)

        vcard.value = response.data.data
        originalValueStr = JSON.stringify(vcard.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const inserting = (phone_number) => !phone_number || (phone_number < 0)
  
const save = async (vcardToSave) => {
  errors.value = null
  console.log(inserting(props.phone_number))
  if (inserting(props.phone_number)) {
    try {
      const response = await axios.post('/vcards', vcardToSave)
      console.dir(response.data)
      vcard.value = response.data.data
      originalValueStr = JSON.stringify(vcard.value)
      toast.success('Vcard was registered successfully.')
      socket.emit('insertedUser', vcard.value)
      
      router.push({name: 'Login'})
    } catch (error) {
      console.log(error)
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('vCard was not registered due to validation errors!')
      } else {
        toast.error('vCard was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('vcards/' + props.phone_number, vcardToSave)
      vcard.value = response.data.data
      originalValueStr = JSON.stringify(vcard.value)
      toast.success('vCard ' + vcard.value.phone_number + ' was updated successfully.')
      router.push({name: 'Dashboard'})
      if (userStore.userType == 'V') {
        await userStore.loadUser()
      }
      socket.emit('updatedUser', vcard.value)
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('vCard ' + props.phone_number + ' was not updated due to validation errors!')
      } else {
        toast.error('vCard ' + props.phone_number + ' was not updated due to unknown server error!')
      }
    }
  }
}

const cancel =  () => {
  router.back()
}

const deleteVcard = async () => {  
  try {
      const response = await axios.delete('vcards/' + props.phone_number)
      userStore.clearUser()
      console.log(response.data.success)
      toast.success('Vcard' + props.phone_number + ' was deleted successfully.')
      router.push({name: 'home'})
    } catch (error) {
      toast.error(error.response.data.message)  
      return false    
    }
    
}

const blockVcard =  () => {
  console.log('blockVcard')
  try {
    console.log(vcard.value.phone_number)
      const response =  axios.patch('vcards/alterblockedStatus/' + props.phone)
      toast.success('Vcard #' + props.phone + ' was blocked successfully.')
      socket.emit('blockedUser', vcard.value)

    } catch (error) {
      
      console.log(error)
      toast.error('Vcard was not blocked!')      
    }
}

const unblockVcard =  () => {
  console.log('unblockVcard')
  try {
      const response =  axios.patch('vcards/alterblockedStatus/' + props.phone)
      console.log(response)
      toast.success('Vcard #' + props.phone + ' was unblocked successfully.')

    } catch (error) {
      
      console.log(error)
      toast.error('Vcard was not unblocked!')      
    }
}

watch(
  () => props.phone_number,
  (newValue) => {
      loadVcard(newValue)
    },
  {immediate: true}  
)

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = JSON.stringify(vcard.value)
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
  

  <vcard-detail
    :vcard="vcard"
    :errors="errors"
    @save="save"
    @cancel="cancel"
    @deleteVcard = "deleteVcard"
    :inserting="inserting(phone_number)"
  ></vcard-detail>
</template>
