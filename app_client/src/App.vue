<script setup>
import { useRouter,RouterLink, RouterView } from 'vue-router'
import { ref, onMounted } from 'vue'

import axios from 'axios'
import { useToast } from "vue-toastification"

import { useUserStore } from "/src/stores/user.js"

const router = useRouter()

const toast = useToast()


const userStore = useUserStore()

// console.log('userType', userStore.userType)

console.log('id', userStore.userId)

const logout = async () => {
  if (await userStore.logout()) {
    toast.success('User has logged out of the application.')
    clickMenuOption()
    router.push({name: 'home'})
  } else {
    toast.error('There was a problem logging out of the application!')
  }
}

const clickMenuOption = () => {
  const domReference = document.getElementById('buttonSidebarExpandId')
  if (domReference) {
    if (window.getComputedStyle(domReference).display !== "none") {
      domReference.click()
    }
  }
}
</script>


<template>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top flex-md-nowrap p-0 shadow">
    <div class="container-fluid">
      <router-link class="col-md-3 col-lg-2 me-0 px-3" :to="{ name: 'home' }" @click="clickMenuOption">
        <img src="@/assets/vCard.png" alt="" class="d-inline-block align-text-top" id="image">
      </router-link>
      <button id="buttonSidebarExpandId" class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav">
          <li class="nav-item" v-show="!userStore.user">
            <router-link class="nav-link" :class="{ active: $route.name === 'NewVcard'}" :to="{ name: 'NewVcard' }" @click="clickMenuOption">
              <i class="bi bi-person-check-fill"></i>
              Register
            </router-link >
          </li>
          <li class="nav-item" v-show="!userStore.user">
            <router-link class="nav-link" :class="{ active: $route.name === 'Login' }" 
                        :to="{ name: 'Login' }" @click="clickMenuOption">
              <i class="bi bi-box-arrow-in-right"></i>
              Login
            </router-link>
          </li>
          <li class="nav-item dropdown" v-show="userStore.user">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img :src="userStore.userPhotoUrl" class="rounded-circle z-depth-0 avatar-img" alt="avatar image">
              <span class="avatar-text">{{ userStore.userName }}</span>
            </a>  
            <!-- only show dropdown when userStore.userId != null -->
            
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li  v-show="userStore.userType == 'V'">
                <router-link class="dropdown-item"
                            :class="{ active: $route.name == 'Vcard'}"
                            :to="{ name: 'Vcard', params: { phone_number: userStore.userId } }" @click="clickMenuOption">
                  <i class="bi bi-person-square"></i>
                  Profile
                </router-link>
              </li>
              <li v-show="userStore.userType == 'A' ">
                <router-link class="dropdown-item" 
                            :class="{ active: $route.name == 'Administrator'}"
                            :to="{ name: 'Administrator', params: { id: userStore.userId } }" @click="clickMenuOption">
                  <i class="bi bi-person-square"></i>
                  Profile
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" :class="{ active: $route.name === 'ChangePassword' }" 
                            :to="{ name: 'ChangePassword' }" @click="clickMenuOption">
                  <i class="bi bi-key-fill"></i>
                  Change Password
                </router-link>
              </li>
              <li v-show="userStore.userType == 'V'">
                <router-link class="dropdown-item" :class="{ active: $route.name === 'ChangeConfirmationCode' }" 
                            :to="{ name: 'ChangeConfirmationCode' }" @click="clickMenuOption">
                <i class="bi bi-123"></i>
                  Change Confirmation Code
                </router-link>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" @click.prevent="logout">
                  <i class="bi bi-arrow-right"></i>Logout
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
          <ul class="nav flex-column" v-show="userStore.user">
            <li class="nav-item" v-show="userStore.userType == 'V'">
              <router-link class="nav-link" :class="{ active: $route.name === 'Dashboard' }" 
                          :to="{ name: 'Dashboard' }" @click="clickMenuOption"> 
                <i class="bi bi-house"></i>
                Dashboard
              </router-link>
            </li>
            <li class="nav-item" >
                <router-link class="nav-link" :class="{ active: $route.name === 'Categories' }" 
                            :to="{ name: 'Categories' }">
                  <i class="bi bi-files"></i>
                    Categories
                </router-link>
            </li>

           
            <li class="nav-item" v-if="userStore.userType == 'A'">
                <router-link class="nav-link" :class="{ active: $route.name === 'Vcards' }" 
                            :to="{ name: 'Vcards' }">
                  <i class="bi bi-files"></i>
                    Vcards
                </router-link>
            </li>

            <li class="nav-item" v-if="userStore.userType == 'A'" >
                <router-link class="nav-link" :class="{ active: $route.name === 'Administrators' }" 
                              :to="{ name: 'Administrators' }" @click="clickMenuOption">
                  <i class="bi bi-files"></i>
                    Administrators
                </router-link>
            </li>
            <li v-if="userStore.userType == 'V'" class="nav-item">
                <router-link class="nav-link" :class="{ active: $route.name === 'Transactions' }" 
                              :to="{ name: 'Transactions' }" @click="clickMenuOption">
                  <i class="bi bi-files"></i>
                    Transactions
                </router-link>
            </li>
            <li v-if="userStore.userType == 'A'" class="nav-item">
                <router-link class="nav-link" :class="{ active: $route.name === 'CreditTransaction' }" 
                              :to="{ name: 'CreditTransaction' }" @click="clickMenuOption">
                  <i class="bi bi-files"></i>
                    Transactions
                    <i class="ms-3 bi bi-xs bi-plus-circle"></i>
                </router-link>
                <router-link class="link-secondary" :to="{ name: 'NewTransaction' }" aria-label="Add new Transactions">
                  
                </router-link>
            </li>
            <li class="nav-item" >
              <router-link class="nav-link" :class="{ active: $route.name === 'Reports' }"
                        :to="{ name: 'Reports' }" @click="clickMenuOption">
                <i class="bi bi-bar-chart-line"></i>
                Reports
              </router-link>
            </li>
          </ul>
        </div>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<style>
@import "./assets/dashboard.css";
#image {
  max-width: 100px;
  height: fit-content;
  
}
.avatar-img {
  margin: -1.2rem 0.8rem -2rem 0.8rem;
  width: 3.3rem;
  height: 3.3rem;
}

.avatar-text {
  line-height: 2.2rem;
  margin: 1rem 0.5rem -2rem 0;
  padding-top: 1rem;
}

.dropdown-item {
  font-size: 0.875rem;
}

.btn:focus {
  outline: none;
  box-shadow: none;
}

#sidebarMenu {
  overflow-y: auto;
}
</style>
