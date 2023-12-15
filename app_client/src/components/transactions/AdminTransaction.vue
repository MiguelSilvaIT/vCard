<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch , computed, onMounted} from 'vue'
import AdminTransactionDetail from './AdminTransactionDetail.vue'
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from '../../stores/user'

const userStore = useUserStore()
const toast = useToast()
const router = useRouter()

const props = defineProps({
    id: {
      type: Number,
      default: null
    }
})

const NewTransaction = () => {
    return {
        id: null,
        vcard:null,
        payment_type: 'MBWAY',
        payment_ref: '', 
        type: 'C',
        description: '',
        category_id: null,
        value: null
    }
} 

const newExternalTransaction = () => {
    return {
        type: ' MB',
        reference: null, 
        value: null
    }
}
const externalTransaction = ref(newExternalTransaction())
const transaction = ref(NewTransaction())

const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)

let originalValueStr = JSON.stringify(transaction.value)

const save =  async() => {
    
    try{
      const response = await axios.post('transactions', transaction.value)
      console.dir(response.data)
      if(response.data.success){
        toast.success('Transaction Created')
        console.dir(response.data)
        originalValueStr=JSON.stringify(transaction.value)
        router.push({name: 'Dashboard'})
      }
      else{
        toast.error(response.data.message)
      }
    }
    catch(error){
      errors.value = error.response.data.errors
    
      toast.error("Validation Error")
    }
}

const cancel =  () => {
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
    let newValueStr = JSON.stringify(transaction.value)
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
    
    <admin-transaction-detail
      :errors="errors"
      :transaction="transaction"
      @save="save"
      @cancel="cancel"
    ></admin-transaction-detail>
  </template>