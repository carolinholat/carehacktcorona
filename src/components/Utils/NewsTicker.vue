<template>
    <v-card class="sidebar" style="min-height: 50px">
        <div class="cardContent">
            <h3 style="margin-bottom: 20px">News</h3>
            <h6 v-for="frage in fragen" :key="frage.ID" @click="$emit('selectFrage', frage)">{{frage.frage}}</h6>
        </div>
    </v-card>
</template>

<script>
    export default {
        name: "NewsTicker",
        props: {
            fragen: Array
        },
        computed: {
           filteredFragen() {
               return this.fragen.slice(0, 5);
           }
        }
    }
</script>

<style scoped>

    h6:hover {
        color: blue;
    }

</style>