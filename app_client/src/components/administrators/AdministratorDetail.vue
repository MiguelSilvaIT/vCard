<script setup>

import { ref, watch, computed, inject } from "vue";

import { useUserStore } from "/src/stores/user.js"

const userStore = useUserStore()

const props = defineProps({
  administrator: {
    type: Object,
    required: true,
  },


  errors: {
    type: Object,
    required: false,
  },
});


const emit = defineEmits(["save", "cancel"]);

const newAdministrator = ref(props.administrator)


//console.log("editingAdministrator -->" , editingAdministrator.value)






const save = () => {
  console.log("editingAdministrator -->", newAdministrator.value)
  emit("save", newAdministrator.value);
}

const cancel = () => {
  emit("cancel", newAdministrator.value);
}



</script>

<template>
  <div>
    <h3 class="mt-5 mb-3">New Administrator</h3>
    <hr />

    <div class="mb-3 flex-grow-1">
      <label for="inputName" class="form-label">Name</label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['name'] : false }" id="inputName"
        placeholder="Administrator Name" required v-model="newAdministrator.name" />
      <field-error-message :errors="errors" fieldName="name"></field-error-message>
    </div>

    <div class="mb-3 ms-xs-3 flex-grow-1">
      <label for="inputEmail" class="form-label">Email</label>
      <input type="text" class="form-control" :class="{ 'is-invalid': errors ? errors['email'] : false }" id="inputEmail"
        placeholder="Administrator Email" required v-model="newAdministrator.email" />
      <field-error-message :errors="errors" fieldName="email"></field-error-message>
    </div>

    <div class="mb-3 ms-xs-3 flex-grow-1">
      <label for="inputPass" class="form-label">Password</label>
      <input type="password" class="form-control" :class="{ 'is-invalid': errors ? errors['password'] : false }"
        id="inputPass" placeholder="Administrator Password" required v-model="newAdministrator.password" />
      <field-error-message :errors="errors" fieldName="password"></field-error-message>
    </div>

    <div class="mb-3 ms-xs-3 flex-grow-1">
      <label for="inputPassConfirm" class="form-label">Confirm Password</label>
      <input type="password" class="form-control" id="inputPassConfirm" placeholder="Confirm Password" required
        v-model="newAdministrator.password_confirmation" />
    </div>

    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
    </div>
  </div>
</template>




<style scoped>
.total_hours {
  width: 26rem;
}
</style>
