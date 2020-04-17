<template>
    <div>
        <v-toolbar style="background: cadetblue">

            <div class="flex-grow-1"></div>

            <div>
                <v-btn text>
                    <router-link to="/" class="routerLink">Home</router-link>
                </v-btn>
                <v-btn text>
                    <router-link to="/about" class="routerLink">Infos</router-link>
                </v-btn>
            </div>

        </v-toolbar>
    </div>
</template>

<script>
    export default {
        name: "TitleBar",
    }
</script>

<style scoped>
    .routerLink {
        text-decoration: none;
        color: whitesmoke;
    }
    .routerLink:hover {
        text-decoration: none;
        color: darkslategrey;
    }

</style>