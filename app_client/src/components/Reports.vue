<template>
    <div>
        <h1>Estatísticas</h1>

        <h2>Despesas por mês</h2>
        <ul>
            <li v-for="(expense, index) in expenses" :key="index">
                {{ expense.month }}: R$ {{ expense.amount }}
            </li>
        </ul>

        <h2>Ganhos por mês</h2>
        <ul>
            <li v-for="(income, index) in incomes" :key="index">
                {{ income.month }}: R$ {{ income.amount }}
            </li>
        </ul>

        <h2>Número de transações por mês</h2>
        <ul>
            <li v-for="(transaction, index) in transactions" :key="index">
                {{ transaction.month }}: {{ transaction.count }} transações
            </li>
        </ul>

        <h2>Histórico do balanço</h2>
        <div>
            <!-- Aqui você pode adicionar um gráfico para exibir o histórico do balanço -->
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            expenses: [
                { month: 'Janeiro', amount: 100 },
                { month: 'Fevereiro', amount: 200 },
                { month: 'Março', amount: 150 },
            ],
            incomes: [
                { month: 'Janeiro', amount: 500 },
                { month: 'Fevereiro', amount: 600 },
                { month: 'Março', amount: 700 },
            ],
            transactions: [
                { month: 'Janeiro', count: 10 },
                { month: 'Fevereiro', count: 15 },
                { month: 'Março', count: 20 },
            ],
        };
    },
};
</script>

<style scoped>
/* Estilos específicos para o componente Reports.vue */
</style>
