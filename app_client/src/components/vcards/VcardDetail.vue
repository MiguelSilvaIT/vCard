<script setup>
import { ref, watch, computed, inject } from "vue";

import { useUserStore } from "/src/stores/user.js"

import avatarNoneUrl from '@/assets/avatar-none.png'


const userStore = useUserStore()

const serverBaseUrl = inject("serverBaseUrl");



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

const emit = defineEmits(["save", "cancel", "blockVcard", "unblockVcard", "deleteVcard"]);

const editingVcard = ref(props.vcard)


watch(
  () => props.vcard,
  (newVcard) => {
    editingVcard.value = newVcard
  },
  { immediate: true }
)


const photoFullUrl = computed(() => {
  return editingVcard.value.photo
    ? serverBaseUrl + "/storage/fotos/" + editingVcard.value.photo
    : avatarNoneUrl
})




const save = () => {
  emit("save", editingVcard.value);
}

const cancel = () => {
  emit("cancel", editingVcard.value);
}

const deleteVcard = () => {
  emit("deleteVcard", editingVcard.value);
}

const blockVcard = () => {
  emit("blockVcard", editingVcard.value);
}

const unblockVcard = () => {
  emit("unblockVcard", editingVcard.value);
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

        <div class="w-25">
          <div class="mb-3">
            <label class="form-label">Photo</label>
            <div class="form-control text-center">
              <img :src="photoFullUrl" class="w-100" />
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Bloqueado?</label>
          <div class="form-control text-center">
            <h1 v-if="editingVcard.blocked === 0" class="text-success">Não Bloqueado</h1>
            <h1 v-if="editingVcard.blocked === 1" class="text-danger">Bloqueado</h1>
          </div>
        </div>


        <!--Balance-->
        <div class="mb-3">
          <label class="form-label">Balance</label>
          <div class="form-control text-center">
            <h1 class="display-1" v-text="editingVcard.balance"></h1>
          </div>
        </div>

        <!--Débito Limite-->
        <div class="mb-3 px-1">
          <label for="inputMaxDebit" class="form-label">Débito Máximo</label>
          <input type="number" class="form-control" :class="{ 'is-invalid': errors ? errors['max_debit'] : false }"
            id="inputMaxDebit" placeholder="MaxDebit" required v-model="editingVcard.max_debit" />
          <field-error-message :errors="errors" fieldName="max_debit"></field-error-message>
        </div>

        <div class="mb-3 d-flex justify-content-end">
          <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
          <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>
          <button type="button" class="btn btn-danger px-5" @click="deleteVcard">Delete</button>
          <button type = "button" v-if="editingVcard.blocked === 0" class="btn btn-warning" @click="blockVcard">Bloquear</button>
          <button type = "button" v-if="editingVcard.blocked === 1" class="btn btn-sucess" @click="unblockVcard">Desbloquear</button>
          
        </div>




      </div>
    </div>
  </form>
</template>

<style scoped>
.total_hours {
  width: 26rem;
}
</style>
