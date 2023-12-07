<script setup>
import { inject } from "vue";

const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  transactions: {
    type: Array,
    default: () => [],
  },
  showVcard: {
    type: Boolean,
    default: true,
  },
  showPaymentType:{
    type: Boolean,
    default: true,
  },
  showPaymentReference:{
    type: Boolean,
    default: true,
  },
  showType: {
    type: Boolean,
    default: true,
  },
  showValue: {
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

const editClick = (transaction) => {
  emit("edit", transaction);
};
</script>

<template>

  

  <table class="table">
    <thead>
      <tr>
        <th v-if="showVcard" class="align-middle">Vcard</th>
        <th v-if="showPaymentType" class="align-middle">Payment Type</th>
        <th v-if="showPaymentReference" class="align-middle">Payment Reference</th>
        <th v-if="showType" class="align-middle">Type</th>
        <th v-if="showValue" class="align-middle">Value</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="transaction in transactions" :key="transaction.id">
        <td v-if="showVcard" class="align-middle">{{ transaction.vcard }}</td>
        <td v-if="showPaymentType" class="align-middle">{{ transaction.payment_type }}</td>
        <td v-if="showPaymentReference" class="align-middle">{{ transaction.payment_reference }}</td>
        <td v-if="showType" class="align-middle">{{ transaction.type }}</td>
        <td v-if="showValue" class="align-middle">{{  transaction.value }}</td>
        <td class="text-end align-middle" v-if="showEditButton">
          <div class="d-flex justify-content-end">
            <button
              class="btn btn-xs btn-light"
              @click="editClick(transaction)"
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