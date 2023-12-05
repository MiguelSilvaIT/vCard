<script setup>
import { inject } from "vue";
import avatarNoneUrl from '@/assets/avatar-none.png'

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  vcards: {
    type: Array,
    default: () => [],
  },
  showPhoneNumber: {
    type: Boolean,
    default: true,
  },
  showEmail: {
    type: Boolean,
    default: true,
  },
  showName: {
    type: Boolean,
    default: true,
  },
  showEditButton: {
    type: Boolean,
    default: true,
  },
 
});

const emit = defineEmits(["edit"]);

// const photoFullUrl = (user) => {
//   return user.photo_url
//     ? serverBaseUrl + "/storage/fotos/" + user.photo_url
//     : avatarNoneUrl;
// };

const editClick = (vcard) => {
  emit("edit", vcard);
};
</script>

<template>

  

  <table class="table">
    <thead>
      <tr>
        <th v-if="showPhoneNumber" class="align-middle">Phone Number</th>
        <th v-if="showName" class="align-middle">Name</th>
        <th v-if="showEmail" class="align-middle">Email</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="vcard in vcards" :key="vcard.phone">
        <td v-if="showPhoneNumber" class="align-middle">{{ vcard.phone }}</td>
        <td v-if="showName" class="align-middle">{{ vcard.name }}</td>
        <td v-if="showEmail" class="align-middle">{{  vcard.email }}</td>
        <td class="text-end align-middle" v-if="showEditButton">
          <div class="d-flex justify-content-end">
            <button
              class="btn btn-xs btn-light"
              @click="editClick(vcard)"
              v-if="showEditButton"
            >
              <i class="bi bi-xs bi-pencil"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  
</template>

<style scoped>
button {
  margin-left: 3px;
  margin-right: 3px;
}

.img_photo {
  width: 3.2rem;
  height: 3.2rem;
}
</style>
