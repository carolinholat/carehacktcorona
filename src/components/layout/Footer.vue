<template>
    <v-footer padless style="background:  #87CEEB" absolute="absolute">
        <!--pro juventa: #342484 -->
        <v-col
                class="text-center"
                cols="12"
        >
            <v-btn text>
                <router-link to="/impressum" class="routerLink">IMPRESSUM</router-link>
            </v-btn>
        </v-col>
    </v-footer>
</template>

<script>
    export default {
        name: "Footer"
    }
</script>

<style scoped>
    .routerLink {
        text-decoration: none;
        color: black;
    }

    .routerLink:hover {
        text-decoration: none;
       /* color: yellow; */
        color: #0d47a1;
    }

</style>