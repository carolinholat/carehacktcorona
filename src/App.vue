<template>
    <v-app>
        <TitleBar :itemsSuche="items"
        @showMeldung="showMeldung($event)"/>
        <router-view
                style="overflow: scroll"
        @loadItems="loadItems($event)"/>
        <Footer dark="true"/>
        <v-content style="overflow: scroll" :meldungId="meldungId">
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
            items: [{text:'abc', value: 1}, {text:'def', value: 2}],
            meldungId: 0
        }),
        methods: {
            showMeldung(id) {
                this.meldungId = id;
            },

            loadItems(items) {
                this.items = [];
                for (let item of items) {
                    let itemObj = {};
                    itemObj.text = item.frage;
                    itemObj.value = item.ID;
                    this.items.push(itemObj);
                }
            }
        }
    };
</script>
