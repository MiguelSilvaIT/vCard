<script setup>

import { ref, watch, computed, inject } from "vue";
import InputText from "primevue/inputtext";

import { useUserStore } from "/src/stores/user.js"

const userStore = useUserStore()

const props = defineProps({
  administrator: {
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


const emit = defineEmits(["save", "cancel"]);

const editingAdmin = ref(props.administrator)

watch(
  () => props.administrator,
  (newCategory) => {
    editingAdmin.value = newCategory
    console.log("Editing Administrator",editingAdmin.value)
  },
  { immediate: true }
)

const administratorTitle = computed( () => {
  if (!editingAdmin.value) {
    return ''
  }
  // console.log(editingCategory.value)
  return props.operationType == 'insert' ? 'New Administrator' : 'Admin #' + editingAdmin.value.id
})


const save = () => {
  emit("save", editingAdmin.value);
}

const cancel = () => {
  emit("cancel", editingAdmin.value);
}



</script>

<template>
  <form class="row g-3 needs-validation" novalidate v-if="operationType == 'insert'" @submit.prevent="save">
    <h3 class="mt-5 mb-3">{{administratorTitle}}</h3>
    <hr />
    <div class="d-flex flex-wrap mt-4 justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-5" >
          <span class="p-float-label">
            <InputText type="text" required v-model="editingAdmin.name"
                  :class="{ 'p-invalid': errors ? errors['name'] : false }" id="inputName" />
            <label for="inputName">Name</label>
            <field-error-message :errors="errors" fieldName="name"></field-error-message>
          </span> 
        </div>
        <div class="col mb-5 ms-xs-3">
          <span class="p-float-label">
            <InputText type="text" required v-model="editingAdmin.email"
                  :class="{ 'p-invalid': errors ? errors['email'] : false }" id="inputEmail" />
            <label for="inputEmail">Email</label>
            <field-error-message :errors="errors" fieldName="email"></field-error-message>
          </span> 
        </div>


        <div class="col mb-5 ms-xs-3" >
          <span class="p-float-label">
            <InputText type="text" required v-model="editingAdmin.password"
                  :class="{ 'p-invalid': errors ? errors['password'] : false }"/>
            <label for="inputPass">Password</label>
            <field-error-message :errors="errors" fieldName="password"></field-error-message>
          </span> 
        </div>

        <div class="col mb-5 ms-xs-3">
          <span class="p-float-label">
            <InputText type="text" required v-model="editingAdmin.password_confirmation"
                  :class="{ 'p-invalid': errors ? errors['password_confirmation'] : false }"/>
            <label for="inputPass">Password Confirmation</label>
            <field-error-message :errors="errors" fieldName="password_confirmation"></field-error-message>
          </span> 
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </form>

  <form class="row g-3 needs-validation" novalidate v-if="operationType == 'update'" @submit.prevent="save">
    <h3 class="mt-5 mb-3">{{administratorTitle}}</h3>
    <hr />
    <div class="d-flex flex-wrap mt-4 justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-5" >
          <span class="p-float-label">
            <InputText type="text" required v-model="editingAdmin.name"
                  :class="{ 'p-invalid': errors ? errors['name'] : false }" id="inputName" />
            <label for="inputName">Name</label>
            <field-error-message :errors="errors" fieldName="name"></field-error-message>
          </span> 
        </div>
        <div class="col mb-5 ms-xs-3">
          <span class="p-float-label">
            <InputText type="text" required v-model="editingAdmin.email"
                  :class="{ 'p-invalid': errors ? errors['email'] : false }" id="inputEmail" />
            <label for="inputEmail">Email</label>
            <field-error-message :errors="errors" fieldName="email"></field-error-message>
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
