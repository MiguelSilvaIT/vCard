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
        type: computed(() => userStore.userType === 'A' ? '' : 'D'),
        description: '',
        category_id: null,
        value: null
    }
}

const transaction = ref(NewTransaction())


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


    const save =  () => {
    if (operation.value == 'insert') 
    {
        console.log(transaction.value)
        transaction.value.vcard = userStore.userId.toString()
        transaction.value.type = 'D'
        if(transaction.value.payment_type == 'VCARD'){
        transaction.value.pair_vcard = transaction.value.payment_ref
        }
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