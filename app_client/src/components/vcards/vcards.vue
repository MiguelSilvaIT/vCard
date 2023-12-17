<script setup>
  import axios from 'axios'
  import { ref, computed, onMounted } from 'vue'
  import VcardTable from './VcardTable.vue'
  import {useRouter} from 'vue-router';
  import { useToast } from "vue-toastification"

  const toast = useToast()
  const router = useRouter()

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
  
  
  const editVcard = (vcard) => {
    router.push({name: 'Vcard', params: {phone: vcard.phone}})
  }


  const deletedVcard = (vcard) => {
    try {
      axios.delete(`vcards/${vcard.phone_number}`)
      //fix policy authorization
      let idx = vcards.value.findIndex((t) => t.phone_number === vcard.phone_number)
      if (idx >= 0) {
        vcards.value.splice(idx, 1)
      }
      toast.success('Vcard #' + vcard.id + ' was deleted successfully.')
    } catch (error) {
      console.log(error)
      toast.error('Vcard was not deleted!')      
    }
  }

  const block = (vcard) => {
    try {
      const response = axios.patch(`vcards/alterblockedStatus/${vcard.phone_number}`)
    } catch (error) {
      console.log(error)
      toast.error('Vcard was not blocked!')      
    }
      let idx = vcards.value.findIndex((t) => t.phone_number === vcard.phone_number)
      if (idx >= 0) {
        console.log(vcards.value[idx].blocked)
        vcards.value[idx].blocked= !vcard.blocked
      }
      toast.success('Vcard #' + vcard.phone_number + ' was blocked successfully.')
  }

  const vcards = ref([])
  
  const filterByEmail = ref(-1)

  

  const filteredVcards = computed( () => {
    return vcards.value.filter(t =>
        (filterByEmail.value == -1
          || filterByEmail.value == t.email
        ))
  })

  

  
  onMounted (() => {
    loadVcards()
  })
</script>

<template>
  <h3 class="mt-5 mb-3">Vcards</h3>
  <div class="d-flex justify-content-between">
 
  </div>
  <hr>

  <vcard-table
    :vcards="vcards"
    @block="block"
    @delete="deletedVcard"
    @edit="editVcard"
    @deleted="deletedVcard"
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
