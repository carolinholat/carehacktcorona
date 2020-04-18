<template>
    <v-footer padless style="background: #342484" absolute="absolute">
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
        color: whitesmoke;
    }

    .routerLink:hover {
        text-decoration: none;
        color: yellow;
    }

</style>