<script setup>
  import axios from 'axios'
  import { ref, computed, onMounted } from 'vue'
  import VcardTable from './VcardTable.vue'
  import {useRouter} from 'vue-router';

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


  const deletedVcard = (deleteVcard) => {
      let idx = vcards.value.findIndex((t) => t.id === deletedVcard.id)
      if (idx >= 0) {
        vcards.value.splice(idx, 1)
      }
  }

  /*const props = defineProps({
    vcardsTitle: {
      type: String,
      default: 'Vcards'
    },
    
  })*/

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
