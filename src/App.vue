<template>
    <v-app>
        <TitleBar/>
        <router-view/>
        <Footer dark="true"/>
        <v-content>
            <!--<HelloWorld/> -->
        </v-content>
    </v-app>
</template>

<script>
    import TitleBar from "./components/layout/TitleBar";
    import Footer from "./components/layout/Footer";

    export default {
        name: 'App',
        components: {
            Footer,
            TitleBar,
        },
        data: () => ({
            //
        }),
    };
</script>
