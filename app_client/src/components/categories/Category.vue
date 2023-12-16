<script setup>
import axios from 'axios'
import { useToast } from "vue-toastification"
import { ref, watch , computed} from 'vue'
import CategoryDetail from "./CategoryDetail.vue"
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from "/src/stores/user.js"


const toast = useToast()
const router = useRouter()

const userStore = useUserStore()

const endpoint = userStore.userType === 'A' ? 'default_categories' : 'categories';

const props = defineProps({
    id: {
      type: Number,
      default: null
    }
})

const newCategory = () => {
    return {
      id: null,
      type: '',
      name: '',
    }
}

const category = ref(newCategory())

const errors = ref(null)
const confirmationLeaveDialog = ref(null)
// String with the JSON representation after loading the project (new or edit)
let originalValueStr = ''

const loadCategory = async (id) => {
  originalValueStr = ''
  errors.value = null
  if (!id || (id < 0)) {
    category.value = newCategory()
    originalValueStr = JSON.stringify(category.value)
  } else {
      try {
        console.log(`${endpoint}/${id}`)
        const response = await axios.get(`${endpoint}/${id}`)
        category.value = response.data.data
        originalValueStr = JSON.stringify(category.value)
      } catch (error) {
        console.log(error)
      }
  }
}

const operation = computed( () => (!props.id || props.id < 0) ? 'insert' : 'update')


const save =  () => {
  if (operation.value == 'insert') 
  {
    axios.post(`${endpoint}`, category.value)
      .then((response) => {
        toast.success('Category Created')
        console.dir(response.data.data)
        originalValueStr = JSON.stringify(category.value)
        router.push({name:'Categories'})
      })
      .catch((error) => {
        if (error.response.status == 422) {
          errors.value = error.response.data.errors
          toast.error("Validation Error")
      }
      })
  } else {
    axios.put(`${endpoint}/${category.value.id}`, category.value)
      .then((response) => {
        toast.success('Category Updated')
        console.dir(response.data.data)
        originalValueStr = JSON.stringify(category.value)
        router.push({name:'Categories'})
      })
      .catch((error) => {
        errors.value = error.response.data.errors
        toast.error("Validation Error")
        console.dir(error)
      })
  }
}

const cancel =  () => {
  router.back()
}

watch(
  () => props.id,
  (newValue) => {
      loadCategory(newValue)
    },
  {immediate: true}  
)

let nextCallBack = null
const leaveConfirmed = () => {
  if (nextCallBack) {
    nextCallBack()
  }
}

onBeforeRouteLeave((to, from, next) => {
  nextCallBack = null
  let newValueStr = JSON.stringify(category.value)
  if (originalValueStr != newValueStr) {
    // Some value has changed - only leave after confirmation
    nextCallBack = next
    confirmationLeaveDialog.value.show()
  } else {
    // No value has changed, so we can leave the component without confirming
    next()
  }
})

</script>

<template>
  <confirmation-dialog
    ref="confirmationLeaveDialog"
    confirmationBtn="Discard changes and leave"
    msg="Do you really want to leave? You have unsaved changes!"
    @confirmed="leaveConfirmed"
  >
  </confirmation-dialog>  

  <category-detail
    :operationType="operation"
    :category="category"
    :errors="errors"
    @save="save"
    @cancel="cancel"
    @deleteCategory = "deleteCategory"
  ></category-detail>
</template>
