import axios from 'axios'
import { ref, computed,inject } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from "./user.js"
import { useToast } from "vue-toastification"

export const useProjectsStore = defineStore('transactions', () => {
    
    const userStore = useUserStore()
    const socket = inject("socket")
    const toast = useToast()

    const projects = ref([])

    const totalTransactions = computed(() => {
        return transactions.value.length
    })

    // socket.on('newTransaction', (transaction) => {
    //     transaction.value.push(transaction)
    //     toast.success(`A new transaction was made (#${transaction.id} : ${transaction.name})`)
    // })

    // socket.on('updateProject', (project) => {
    //     updateProjectOnArray(project)
    //     toast.info(`The project (#${project.id} : ${project.name}) was updated!`)
    // })
    // socket.on('deleteProject', (project) => {
    //     deleteProjectOnArray(project)
    //     toast.info(`The project (#${project.id} : ${project.name}) was deleted!`)
    // })

    // const myTransactions = computed(() => {        
    //     return transactions.value.filter( transacation => 
    //         transacation.vcard == userStore.userId)
    // })

    //  

    function getProjectsByFilter(responsibleId, status) {
        return projects.value.filter( prj =>
            (!responsibleId || responsibleId == prj.responsible_id) &&
            (!status || status == prj.status)
        )
    }
    
    function getProjectsByFilterTotal(responsibleId, status) {
        return projects.value.reduce((acumulador, prj) =>
            ((!responsibleId || responsibleId == prj.responsible_id) &&
                (!status || status == prj.status)    )
                ? acumulador + 1 
                : acumulador
            , 0)        
    }

    function clearProjects() {
        projects.value = []
    }

    async function loadTransactions() {
        try {
            const response = await axios.get('transactions')
            transactions.value = response.data.data
            return transactions.value
        } catch (error) {
            clearProjects()
            throw error
        }
    }
    
    async function insertTransaction(newTransaction) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the insertProject
        const response = await axios.post('transactions', newTransaction)
        transactions.value.push(response.data.data)
        //socket.emit('newTransaction', response.data.data)
        return response.data.data
    }

    function updateTransactionOnArray(project) {
        let idx = transactions.value.findIndex((t) => t.id === transaction.id)
        if (idx >= 0) {
            transactions.value[idx] = transaction
        }
    }

    async function updateTransaction(updateTransaction) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the updateProject
        const response = await axios.put('transactions/' + updateTransaction.id, updateTransaction)
        updateProjectOnArray(response.data.data)
        //socket.emit('updateProject', response.data.data)
        return response.data.data
    }

    function deleteProjectOnArray(project) {
        let idx = projects.value.findIndex((t) => t.id === project.id)
        if (idx >= 0) {
            projects.value.splice(idx, 1)
        }
    }

    async function deleteProject( deleteProject) {
        // Note that when an error occours, the exception should be
        // catch by the function that called the deleteProject
        const response = await axios.delete('projects/' + deleteProject.id)
        deleteProjectOnArray(response.data.data)
        socket.emit('deleteProject', response.data.data)
        return response.data.data
    }  
    
    return {
        projects,
        totalProjects,
        myInprogressProjects,
        totalMyInprogressProjects,
        getProjectsByFilter,
        getProjectsByFilterTotal, 
        loadProjects,
        clearProjects,
        insertProject,
        updateProject,
        deleteProject
    }
})
