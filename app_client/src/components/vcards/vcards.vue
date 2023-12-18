<script setup>
  import axios from 'axios'
  import { ref, computed, onMounted, inject } from 'vue'
  import VcardTable from './VcardTable.vue'
  import {useRouter} from 'vue-router';
  import { useToast } from "vue-toastification"

  const toast = useToast()
  const router = useRouter()
  const socket = inject('socket')

  const loadVcards = () => {
    // Change later when authentication is implemented
    axios.get('vcards')
      .then((response) => {
        vcards.value = response.data.data
        console.log(vcards)
      })
      .catch((error) => {
        console.log(error)
      })
  }
  
  
  const editMaxDebit = async (vcard) => {
    try{
      const response = await  axios.patch(`vcards/${vcard.phone_number}`, vcard)
      if(response.data.success){
        let idx = vcards.value.findIndex((t) => t.phone_number === vcard.phone_number)
        if (idx >= 0) {
          vcards.value[idx].max_debit= response.data.data.max_debit
        }
        toast.success('Vcard #' + vcard.phone_number + ' was edited successfully.')
        socket.emit('max_debit', vcard)
      }
      else{
        toast.error('There was a problem editing the vcard!')
      }
    } catch (error) {
      if(error.response.status == 422)
        errors = error.response.data.errors
      toast.error('Max debit was not updated!')
    }
  }


  const deletedVcard = async (vcard) => {
    try {
      const response = await axios.delete(`vcards/${vcard.phone_number}`)
      //fix policy authorization
      if (response.data.success){
        let idx = vcards.value.findIndex((t) => t.phone_number === vcard.phone_number)
        if (idx >= 0) {
          vcards.value.splice(idx, 1)
        }
        toast.success('Vcard #' + vcard.phone_number + ' was deleted successfully.')
        socket.emit('deletedUser', vcard)
      }
      else
        toast.error('There was a problem deleting the vcard!')
    } catch (error) {
      toast.error(error.response.data.message)  
      return false          
    }
  }

  const block = async (vcard) => {
    try {
      const response = await axios.patch(`vcards/alterblockedStatus/${vcard.phone_number}`)
      let idx = vcards.value.findIndex((t) => t.phone_number === vcard.phone_number)
      if (idx >= 0) {
        console.log(response.data.data.blocked)
        vcards.value[idx].blocked= response.data.data.blocked
        if(response.data.data.blocked){
          toast.success('Vcard #' + vcard.phone_number + ' was blocked successfully.')
          socket.emit('blockedUser', vcard)
        }
        else{
          toast.success('Vcard #' + vcard.phone_number + ' was unblocked successfully.')
        }
      }
    } catch (error) {
      console.log(error)
      toast.error('Vcard was not blocked!')      
    }
      
  }

  const vcards = ref([])
  
  const filterByEmail = ref(-1)

  onMounted (() => {
    loadVcards()
  })
</script>

<template>
  <h3 class="mt-5 mb-3">Vcards</h3>
  <hr>

  <vcard-table
    :vcards="vcards"
    @block="block"
    @delete="deletedVcard"
    @editMaxDebit="editMaxDebit"
  ></vcard-table>
</template>


<style scoped>
.filter-div {
  min-width: 12rem;
}
.total-filtro {
  margin-top: 0.35rem;
}
.btn-addtask {
  margin-top: 1.85rem;
}
</style>
