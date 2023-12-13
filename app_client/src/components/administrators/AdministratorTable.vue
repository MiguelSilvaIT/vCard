<script setup>
import { inject } from "vue";

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  administrators: {
    type: Array,
    default: () => [],
  },
  showId: {
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

  showDeleteButton: {
    type: Boolean,
    default: true,
  },
 
});

const emit = defineEmits([ "delete"]);

// const photoFullUrl = (user) => {
//   return user.photo_url
//     ? serverBaseUrl + "/storage/fotos/" + user.photo_url
//     : avatarNoneUrl;
// };



const deleteClick = (administrator) => {
  emit("delete", administrator);
};
</script>

<template>

  

  <table class="table">
    <thead>
      <tr>
        <th v-if="showId" class="align-middle">#</th>
        <th v-if="showName" class="align-middle">Name</th>
        <th v-if="showEmail" class="align-middle">Email</th>
        <th v-if="showDeleteButton" class="align-middle"></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="administrator in administrators" :key="administrator.id">
        <td v-if="showId" class="align-middle">{{ administrator.id }}</td>
        <td v-if="showName" class="align-middle">{{ administrator.name }}</td>
        <td v-if="showEmail" class="align-middle">{{  administrator.email }}</td>

        <td class="text-end align-middle" v-if="showDeleteButton">
          <div class="d-flex justify-content-end">
            <button
              class="btn btn-xs btn-light"
              @click="deleteClick(administrator)"
              v-if="showDeleteButton"
            >
              <i class="bi bi-xs bi-trash"></i>
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
