<script setup>
import { ref, watch, computed, inject } from "vue";

import { useUserStore } from "/src/stores/user.js"

const userStore = useUserStore()

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },


  errors: {
    type: Object,
    required: false,
  },
});

const emit = defineEmits(["save", "cancel", "blockVcard", "deleteVcard"]);

const editingVcard = ref(props.vcard)

console.log(editingVcard.value)

watch(
  () => props.vcard,
  (newVcard) => {
    editingVcard.value = newVcard
  },
  { immediate: true }
)



const save = () => {
  editingVcard.value.vcard = userStore.userId
  emit("save", editingVcard.value);
}

const cancel = () => {
  emit("cancel", editingVcard.value);
}

const deleteVcard =  () => {
  emit("deleteVcard", editingVcard.value);
}

const blockVcard =  () => {
  emit("blockVcard", editingVcard.value);
}


</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">Vcard #{{ editingVcard.phone }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
    <div class="w-75 pe-4">
        <div class="mb-3">
            <label for="inputName" class="form-label">Name</label>
            <p class="form-control" v-text="editingVcard.name" readonly></p>

        </div>

        <div class="mb-3">
            <label for="inputPhone" class="form-label">Phone Number</label>
            <p class="form-control" v-text="editingVcard.phone" readonly></p>
    
        </div>

        <div class="mb-3">
            <label for="inputEmail" class="form-label">Email</label>
            <p class="form-control" v-text="editingVcard.email" readonly></p>
       
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
