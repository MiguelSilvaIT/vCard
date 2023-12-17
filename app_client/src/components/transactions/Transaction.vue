<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch , computed, inject} from 'vue'
import TransactionDetail from "./TransactionDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from '../../stores/user'

const userStore = useUserStore()
const toast = useToast()
const router = useRouter()
const socket = inject('socket')

const props = defineProps({
    id: {
      type: Number,
      default: null
    }
})

const NewTransaction = () => {
    return {
        id: null,
        vcard:userStore.userId.toString(),
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
    originalValueStr = JSON.stringify(transaction.value)
  } else {
      try {
        const response = await axios.get('transactions/' + id)
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
    
    if(transaction.value.payment_type == 'VCARD'){
      transaction.value.pair_vcard = transaction.value.payment_ref
    }
    try{
      const response = await axios.post('transactions', transaction.value)
      console.dir(response.data)
      if(response.data.success){
        toast.success('Transaction Created')
        socket.emit('newTransaction', response.data.data)
        console.dir(response.data)
        originalValueStr=JSON.stringify(transaction.value)
        router.push({name: 'Transactions'})
      }
      else{
        toast.error(response.data.message)
      }
    }
    catch(error){
      errors.value = error.response.data.errors
      toast.error("Validation Error")
    }
  } else {
    axios.put('transactions/' + props.id, transaction.value)
      .then((response) => {
        toast.success('Transaction Updated')
        console.dir(response.data)
        originalValueStr=JSON.stringify(transaction.value)
        router.push({name: 'Transactions'})
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
  router.back()
}

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
