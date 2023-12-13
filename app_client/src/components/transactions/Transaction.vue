<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch , computed} from 'vue'
import TransactionDetail from "./TransactionDetail.vue"
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
        vcard:'',
        payment_type: 'VCARD',
        payment_ref: '', 
        type:'D',
        description: '',
        category_id: null,
        value: null
    }
}

const NewExternalTransaction = () => {
    return {
        type: 'VCARD',
        reference: '', 
        value: null
    }
}

const transaction = ref(NewTransaction())
const externalTransaction = ref(NewExternalTransaction())

const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const loadTransactions = async (id) => {
  originalValueStr = ''
  errors.value = null
  if (!id || (id < 0)) {
    transaction.value = NewTransaction()
  } else {
      try {
        const response = await axios.get('transactions/' + id)
        // console.log("Response",response.data.data)
        transaction.value = response.data.data
        originalValueStr = JSON.stringify(transaction.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const operation = computed( () => (!props.id || props.id < 0) ? 'insert' : 'update')

const save =  async () => {
  if (operation.value == 'insert') 
  {
    console.log(transaction.value)
    transaction.value.vcard = userStore.userId.toString()
    
    if(transaction.value.payment_type == 'VCARD'){
      transaction.value.pair_vcard = transaction.value.payment_ref
    }
    try{
    const response = await axios.post('transactions', transaction.value)
      console.dir(response.data)
      if(transaction.value.payment_type != "VCARD"){
        externalTransaction.value.type = transaction.value.payment_type
        externalTransaction.value.reference = transaction.value.payment_ref
        externalTransaction.value.value = transaction.value.value
        if(response.data.success){
          axios.post('https://dad-202324-payments-api.vercel.app/api/credit', externalTransaction.value)
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
      }
      else{
        toast.success('Transaction Created')
        router.back()
      }
    }
    catch(error){
      errors.value = error.response.data.errors
      console.log(error)
    }
  } else {
    axios.put('transactions/' + props.id, transaction.value)
      .then((response) => {
        toast.success('Transaction Updated')
        console.dir(response.data)
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
}

const cancel =  () => {
  originalValueStr = JSON.stringify(transaction.value)
  router.back()
}

// const deleteTransaction =  () => {
//   console.log('deleteTransaction')
  
//   try {
//       const response =  axios.delete('transactions/' + props.id)
//       console.log(response)
//       toast.success('Transaction #' + props.id + ' was deleted successfully.')
//       // router.back()

//     } catch (error) {
      
//       console.log(error)
//       toast.error('Category was not deleted!')      
//     }
// }


watch(
  () => props.id,
  (newValue) => {
      loadTransactions(newValue)
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
  
  <transaction-detail
    :operationType="operation"
    :transaction="transaction"
    :errors="errors"
    @save="save"
    @cancel="cancel"
  ></transaction-detail>
</template>
