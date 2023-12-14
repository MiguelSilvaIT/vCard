<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch , inject} from 'vue'
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

const newVcard = () => {
    return {
      name: '',
      email: '',
      photo_url: null,
      phone_number: '',
      password: '',
      password_confirmation: '',
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
      try {
        const response = await axios.get('vcards/' + phone)

        //vcard.value = response.data.data
        vcard.value = response.data.data
        originalValueStr = JSON.stringify(vcard.value)
      } catch (error) {
        console.log(error)
      }
  
}

const inserting = (id) => !id || (id < 0)

const update =  () => {

  console.log('save -->' , JSON.stringify({ max_debit: vcard.value.max_debit }))
  axios.patch('vcards/' + props.phone, JSON.stringify({ max_debit: vcard.value.max_debit }))
    .then((response) => {
      toast.success('Vcard Updated')
      console.dir(response.data.data)
      router.back()

    })
    .catch((error) => {
    if (error.response && error.response.status == 422) {
      errors.value = error.response.data.errors
      toast.error("Validation Error")
    }
      console.dir(error)
    })
  }
  
  const save = async (vcardToSave) => {
  errors.value = null
  if (inserting(props.id)) {
    try {
      const response = await axios.post('vcards', vcardToSave)
      vcard.value = response.data.data
      originalValueStr = JSON.stringify(vcard.value)
      toast.success('Vcard was registered successfully.')
      //socket.emit('insertedUser', user.value)
      
      router.push({name: 'Login'})
    } catch (error) {
      console.log(error)
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('User was not registered due to validation errors!')
      } else {
        toast.error('User was not registered due to unknown server error!')
      }
    }
  } else {
    try {
      const response = await axios.put('vcards/' + props.id, userToSave)
      user.value = response.data.data
      originalValueStr = JSON.stringify(user.value)
      toast.success('User #' + user.value.id + ' was updated successfully.')
      if (user.value.id == userStore.userId) {
        await userStore.loadUser()
      }
      //socket.emit('updatedUser', user.value)

      router.back()
    } catch (error) {
      if (error.response.status == 422) {
        errors.value = error.response.data.errors
        toast.error('User #' + props.id + ' was not updated due to validation errors!')
      } else {
        toast.error('User #' + props.id + ' was not updated due to unknown server error!')
      }
    }
  }
}

const cancel =  () => {
  originalValueStr = JSON.stringify(vcard.value)
  router.back()
}

const deleteVcard =  () => {
  console.log('deleteVcard')
  
  try {
      const response =  axios.delete('vcards/' + props.phone_number)
      console.log(response)
      toast.success('Vcard' + props.phone_number + ' was deleted successfully.')
      
      logout()

    } catch (error) {
      
      console.log(error)
      toast.error('Vcard was not deleted!')      
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

console.log('props.phone_number', props.phone_number)
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
    @blockVcard = "blockVcard"
    @unblockVcard = "unblockVcard"
    @update = "update"
    :inserting = "inserting"
  ></vcard-detail>
</template>
