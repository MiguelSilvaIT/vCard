<script setup>
import { ref, watch, computed, inject } from "vue";

import ConfirmPopup from 'primevue/confirmpopup';
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
const inputPhotoFile = ref(null)
const editingImageAsBase64 = ref(null)
const deletePhotoOnTheServer = ref(false)


watch(
  () => props.vcard,
  (newVcard) => {
    editingVcard.value = newVcard
  },
  { immediate: true }
)


const photoFullUrl = computed(() => {
  if (deletePhotoOnTheServer.value) {
    return avatarNoneUrl
  }
  if (editingImageAsBase64.value) {
    return editingImageAsBase64.value
  } else {
    return editingVcard.value.photo_url
      ? serverBaseUrl + "/storage/fotos/" + editingVcard.value.photo_url
      : avatarNoneUrl
  }
})




const save = () => {
  const userToSave = editingVcard.value
  userToSave.deletePhotoOnServer = deletePhotoOnTheServer.value
  userToSave.base64ImagePhoto = editingImageAsBase64.value
  emit("save", userToSave);
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

const changePhotoFile = () => {
  try {
    const file = inputPhotoFile.value.files[0]
    if (!file) {
      editingImageAsBase64.value = null
    } else {
      const reader = new FileReader()
      reader.addEventListener(
          'load',
          () => {
            // convert image file to base64 string
            editingImageAsBase64.value = reader.result
            deletePhotoOnTheServer.value = false
          },
          false,
      )
      if (file) {
        reader.readAsDataURL(file)
      }
    }
  } catch (error) {
    editingImageAsBase64.value = null
  }
}
const resetToOriginalPhoto = () => {
  deletePhotoOnTheServer.value = false
  inputPhotoFile.value.value = ''
  changePhotoFile()
}

const cleanPhoto = () => {
  deletePhotoOnTheServer.value = true
}

</script>

<template>
  <form class="row g-3 needs-validation" novalidate @submit.prevent="save">
    <h3 class="mt-5 mb-3">Vcard {{ userStore.userId }}</h3>
    <hr />
    <div class="d-flex flex-wrap justify-content-between">
      <div class="w-75 pe-4">
        <div class="mb-3">
          <label for="inputName" class="form-label">Nome</label>
          <input
            type="text"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['name'] : false }"
            id="inputName"
            placeholder="Nome"
            required
            v-model="editingVcard.name"
          />
          <field-error-message :errors="errors" fieldName="name"></field-error-message>
        </div>

        <div class="mb-3 px-1">
          <label for="inputEmail" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            :class="{ 'is-invalid': errors ? errors['email'] : false }"
            id="inputEmail"
            placeholder="Email"
            required
            v-model="editingVcard.email"
          />
          <field-error-message :errors="errors" fieldName="email"></field-error-message>
        </div>
        
      </div>
      <div class="w-25">
        <div class="d-flex flex-column">
          <div class="form-control text-center">
            <img :src="userStore.userPhotoUrl" class="w-100" />
          </div>
          <div class="mt-3 d-flex justify-content-between flex-wrap">
            <label for="inputPhoto" class="btn btn-dark flex-grow-1 mx-1">Carregar</label>
            <button class="btn btn-secondary flex-grow-1 mx-1" @click.prevent="resetToOriginalPhoto" v-if="editingVcard.photo_url">Repor</button>
            <button class="btn btn-danger flex-grow-1 mx-1" @click.prevent="cleanPhoto" v-show="editingVcard.photo_url || editingImageAsBase64">Apagar</button>
          </div>
          <div>
            <field-error-message :errors="errors" fieldName="base64ImagePhoto"></field-error-message>
          </div>
        </div>
      </div>
    </div>
    <div class="mb-3 d-flex justify-content-end">
      <button type="button" class="btn btn-primary px-5" @click="save">Save</button>
      <button type="button" class="btn btn-light px-5" @click="cancel">Cancel</button>

      <ConfirmPopup class="btn btn-danger px-5" @click="deleteVcard">Delete</ConfirmPopup>
      
    </div>
  </form>
  <input type="file" style="visibility:hidden;" id="inputPhoto" ref="inputPhotoFile" @change="changePhotoFile" />
</template>

<style scoped>

</style>
