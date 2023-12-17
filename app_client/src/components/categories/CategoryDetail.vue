<script setup>

import { ref, watch, computed, inject } from "vue";

import { useUserStore } from "/src/stores/user.js"
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import InputText from 'primevue/inputtext';

const userStore = useUserStore()

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },

  operationType: {
      type: String,
      default: 'insert'  // insert / update
  },

  errors: {
    type: Object,
    required: false,
  },
});

const categories = [
  { name: 'Debit', value: 'D' },
  { name: 'Credit', value: 'C' },
];

const emit = defineEmits(["save", "cancel", "deleteCategory"]);

const editingCategory = ref(props.category)
//console.log("editingCategory -->" , editingCategory.value)

watch(
  () => props.category,
  (newCategory) => {
    editingCategory.value = newCategory
  },
  { immediate: true }
)

const categoryTitle = computed( () => {
    if (!editingCategory.value) {
        return ''
      }
      // console.log(editingCategory.value)
      return props.operationType == 'insert' ? 'New Category' : 'Category #' + editingCategory.value.id
  })

const save = () => {
  if (userStore.userType == 'V'){
    editingCategory.value.vcard = userStore.userId

  }
  emit("save", editingCategory.value);
}

const cancel = () => {
  emit("cancel", editingCategory.value);
}

const deleteCategory =  () => {
  emit("deleteCategory", editingCategory.value);
}


</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-2">{{categoryTitle}}</h3>
    <hr />
    <div class="d-flex flex-wrap mt-4 justify-content-between">
      <div class="w-75 pe-4">
        <div class="col mb-5 ms-xs-3">
          <div class="p-float-label">
            <InputText type="text" v-model="editingCategory.name" :class="{ 'p-invalid': errors ? errors['name'] : false }"/>
            <label for="dd-paymentType">Name</label>
            <field-error-message :errors="errors" fieldName="name"></field-error-message>
          </div>
        </div>    
        <div class="mb-5 ">
          <span class="p-float-label">
            <Dropdown v-model="editingCategory.type" :class="{ 'p-invalid': errors ? errors['type'] : false }" :options="categories" optionLabel="name" optionValue="value"/>
            <label for="number-input">Type</label>
            <field-error-message :errors="errors" fieldName="type"></field-error-message>
          </span>
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
