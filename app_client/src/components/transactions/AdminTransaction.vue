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

let originalValueStr = ''
// const loadVcards = async (id) => {
//     let originalValueStr = ''
//     errors.value = null
//     try {
//         const response = await axios.get('vcards/')
//         // console.log("Response",response.data.data)
//         transaction.value = response.data.data
//         originalValueStr = JSON.stringify(transaction.value)
//     } catch (error) {
//         console.log(error)
//     }
// }



const save =  async() => {
    externalTransaction.value.type = transaction.value.payment_type
    externalTransaction.value.reference = transaction.value.payment_ref
    externalTransaction.value.value = transaction.value.value
    // transaction.value.vcard.phone = transaction.value.vcard.phone
    try{
        const response = await axios.post('https://dad-202324-payments-api.vercel.app/api/debit', externalTransaction.value)
        console.dir(response.data)
        if(response.data.status == "valid"){
            console.log("Valid", transaction.value)
            axios.post('transactions', transaction.value)
                .then((response) => {
                    toast.success('Transaction Created')
                    console.dir(response.data)
                    router.back()
                })
                .catch((error) => {
                    if (error.response.status == 422) {
                    errors.value = error.response.data.errors
                    toast.error("Validation Error")
                }
            })
        }
        else{

        }
    }
    catch(error){
        if (error.response.status == 422) {
                errors.value = error.response.data.errors
        toast.error("Validation Error")
        }
    }
}

const cancel =  () => {
    originalValueStr = JSON.stringify(transaction.value)
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