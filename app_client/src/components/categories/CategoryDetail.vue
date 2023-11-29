<script setup>
import { ref, watch, computed, inject } from "vue";

import { useUserStore } from "/src/stores/user.js"

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

const emit = defineEmits(["save", "cancel", "deleteCategory"]);

const editingCategory = ref(props.category)

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
      return props.operationType == 'insert' ? 'New Task' : 'Task #' + editingTask.value.id
  })

const save = () => {
  editingCategory.value.vcard = userStore.userId
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
    <h3 class="mt-5 mb-3">Category #{{ editingCategory.id }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Name</label>
          <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['name'] : false }"
            id="inputName" placeholder="Category Name" required v-model="editingCategory.name" />
          <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>

        <div class="mb-3 ms-xs-3 flex-grow-1">
          <label for="inputType" class="form-label">Type</label>
          <select class="form-select" id="inputType" v-model="editingCategory.type">
            <option value="C">Crédito</option>
            <option value="D">Débito</option>
          </select>
        </div>

      </div>

    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
      <button type="button" class="btn btn-danger px-5" @click="deleteCategory">Delete</button>

    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
