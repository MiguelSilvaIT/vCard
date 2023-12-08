<script setup>
import { ref, watch, computed, inject } from "vue";

import { useUserStore } from "/src/stores/user.js"

const userStore = useUserStore()

const props = defineProps({
  transaction: {
    type: Object,
    required: true,
  },
  operationType: {
      type: String,
      default: 'insert'  // insert / update
  },
  categories: {
      type: Array,
      required: true
    },
  errors: {
      type: Object,
      required: false,
    },
});
console.log("Props",props)
const emit = defineEmits(["save", "cancel", "deleteTransaction"]);

const editingTransaction = ref(props.transaction)

watch(
  () => props.transaction,
  (newTransaction) => {
    editingTransaction.value = newTransaction
    console.log(props.transaction)
  },
  { immediate: true }
)

const transactionTittle = computed( () => {
    if (!editingTransaction.value) {
        return ''
      }
    //   console.log("Transaction",editingTransaction.value.data)
      return props.operationType == 'insert' ? 'New Transaction' : 'Transaction #' + editingTransaction.value.id
  })

const operationCreacte = computed( () => {
    if (!editingTransaction.value) {
        return ''
      }
      return props.operationType == 'insert' ? true : false
    });

const save = () => {
  emit("save", editingTransaction.value);
}

const cancel = () => {
  emit("cancel", editingTransaction.value);
}

const deleteTransaction =  () => {
  emit("deleteTransaction", editingTransaction.value);
}


</script>

<template>
    
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">{{transactionTittle}}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
        
      <div class="w-75 pe-4">
        <div class="mb-3" v-if="!operationCreacte">
            <label for="inputVcard" class="form-label">Vcard</label>
            <p class="form-control" >{{editingTransaction.vcard}}</p>          
        </div>
        <div class="mb-3">
          <label for="inputDescription" class="form-label">Description</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['description'] : false }"
            id="inputDescription" placeholder="Transaction Description" required v-model="editingTransaction.description" />
          <field-error-message :errors="errors" fieldName="description"></field-error-message>
        </div>
        <div class="mb-3 ms-xs-3 flex-grow-1">
          <label for="inputCategory" class="form-label">Category</label>
          <select class="form-select" id="inputCategory" v-model="editingTransaction.value.category_id">
            <option
                v-for="cat in categories"
                :key="cat.id"
                :value="cat.id"
                >{{cat.name}}</option>
          </select>
          <field-error-message :errors="errors" fieldName="category_id"></field-error-message>
        </div>

      </div>

    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
      <button type="button" class="btn btn-danger px-5" @click="deleteTransaction">Delete</button>
    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
