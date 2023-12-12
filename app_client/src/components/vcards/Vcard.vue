<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch , computed} from 'vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import VcardDetail from "./VcardDetail.vue"


const toast = useToast()
const router = useRouter()

const props = defineProps({
    phone: {
      type: Number,
      default: null
    }
  
})

const newVcard = () => {
    return {
      phone: null,
      name: '',
      email: '',
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



const save =  () => {

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
   

const cancel =  () => {
  originalValueStr = JSON.stringify(vcard.value)
  router.back()
}

const deleteVcard =  () => {
  console.log('deleteVcard')
  
  try {
      const response =  axios.delete('dadvcards/' + props.phone)
      console.log(response)
      toast.success('Vcard #' + props.phone + ' was deleted successfully.')
      // router.back()

    } catch (error) {
      
      console.log(error)
      toast.error('Vcard was not deleted!')      
    }
}

const blockVcard =  () => {
  console.log('blockVcard')
  try {
      const response =  axios.get('dadvcards/alterblockedStatus/' + props.phone)
      console.log(response)
      toast.success('Vcard #' + props.phone + ' was blocked successfully.')
      // router.back()

    } catch (error) {
      
      console.log(error)
      toast.error('Vcard was not blocked!')      
    }
}

const unblockVcard =  () => {
  console.log('unblockVcard')
  try {
      const response =  axios.get('dadvcards/alterblockedStatus/' + props.phone)
      console.log(response)
      toast.success('Vcard #' + props.phone + ' was unblocked successfully.')
      // router.back()

    } catch (error) {
      
      console.log(error)
      toast.error('Vcard was not unblocked!')      
    }
}


watch(
  () => props.phone,
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
  <!-- <confirmation-dialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </confirmation-dialog>   -->

  <vcard-detail
    :vcard="vcard"
    :errors="errors"
    @save="save"
    @cancel="cancel"
    @deleteVcard = "deleteVcard"
    @blockVcard = "blockVcard"
    @unblockVcard = "unblockVcard"
  ></vcard-detail>
</template>
