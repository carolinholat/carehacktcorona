<template>
    <div>
        <h2>User</h2>
        <v-card
                max-width="344"
                class="mx-auto"
                margin-top="margin-top"
                @mousemove.stop=""
        >
            <h3>{{person}}</h3>

        </v-card>
    </div>
    
</template>

<script>
    export default {
        name: "ShowUser",
        props: {
            person: String
        }
    }
</script>

<style scoped>

</style>