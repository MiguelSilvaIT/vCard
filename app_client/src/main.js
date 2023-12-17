//import './assets/main.css'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap-icons/font/bootstrap-icons.css"
import "bootstrap"
import "./assets/main.css"

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { io } from 'socket.io-client'

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import PrimeVue from 'primevue/config';
import 'primevue/resources/themes/bootstrap4-light-blue/theme.css'
// import 'primevue/resources/themes/lara-light-green/theme.css'
import StyleClass from 'primevue/styleclass';
                



import axios from 'axios'

import { FilterService } from 'primevue/api';
import ToastService from 'primevue/toastservice';
import Toast from "vue-toastification"
// Import the Toast CSS (or use your own)!
import "vue-toastification/dist/index.css"

import FieldErrorMessage from './components/global/FieldErrorMessage.vue'
import ConfirmationDialog from './components/global/ConfirmationDialog.vue'
import Dialog from 'vue3-dialog'

import App from './App.vue'
import router from './router'


const app = createApp(App)

const serverBaseUrl = import.meta.env.VITE_API_DOMAIN
const wsConnection = import.meta.env.VITE_WS_CONNECTION

app.provide('socket', io(wsConnection))


app.provide('serverBaseUrl', serverBaseUrl)  
// Default Axios configuration
axios.defaults.baseURL = serverBaseUrl + '/api'
axios.defaults.headers.common['Content-type'] = 'application/json'


// Default/Global Toast configuration
app.use(Toast, {
    position: "top-center",
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: true,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false
})



app.use(createPinia())
app.use(router)
app.use(PrimeVue);
app.use(ToastService);
app.use(FilterService);
app.directive('styleclass', StyleClass);
app.use(Dialog)

app.component('FieldErrorMessage', FieldErrorMessage)
app.component('ConfirmationDialog', ConfirmationDialog)
app.component('VueDatePicker', VueDatePicker);


app.mount('#app')
