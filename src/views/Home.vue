<template>


            <v-row class="main">
                <v-col cols="2" style="background-color: #e1f5fe;">
                    <div class="sidebar">
                        <h3>Geschäftsbereiche</h3>
                        <p class="spaced">Nach Abteilung oder nach Bereich auswählen?</p>
                        <v-radio-group v-model="abteilungOderKategorie" :mandatory="true">
                            <v-radio label="Abteilung" value="abteilung"></v-radio>
                            <v-radio label="Kategorie" value="kategorie"></v-radio>
                        </v-radio-group>
                        <v-btn color="primary" class="spaced">BLA</v-btn>
                    </div>
                </v-col>
                <v-col cols="7" >
                    <div class="sidebar">
                        <p v-if="thema === ''"><b>Themen</b>: Alle (Filter ist oben im Menü)</p>
                        <div v-else class="d-inline-flex">
                            <p ><b>Thema</b>: {{thema}}</p>
                            <v-btn color="primary" style="margin-left: 20px">Thema abonieren</v-btn>
                        </div>


                        <h2 align="center" class="spaced">Übersicht Info-Meldungen</h2>
                        <p style="height: 30px"></p>
                        <div>
                            <h4>Müssen wir Mundschutz tragen</h4>
                            <p class=".font-italic font-weight-light">Frage am: Montag 12.04.2020</p>
                            <p class=".font-italic font-weight-light">Antwort am: 12.04.2020</p>
                            <v-card class="cardContent">Ja</v-card>
                            <v-btn class="spaced">Zum Forum</v-btn>
                        </div>
                    </div>
                </v-col>
                <v-col cols="3" >

                    <v-card class="sidebar" style="min-height: 50px">
                        <div class="cardContent">
                            <h3>News</h3>
                        </div>
                    </v-card>
                    <v-card class="sidebar" style="min-height: 50px; margin-top: 30px">
                        <div class="cardContent">
                            <h3>Kontakt</h3>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

</template>

<script>
    import axios from "../plugins/axios";

    export default {
        components: {},
        methods: {
        },
        data() {
            return {
                step: 1,
                abteilungOderKategorie: 'abteilung',
                thema: ''
            }
        }
    };
</script>

<style>
    .main {
        height: 100%;
    }

    .sidebar {
        margin-left: 50px;
        margin-top: 20px;
        margin-right: 50px;
    }

    .cardContent {
        padding: 20px
    }


    .spaced {
        margin: 10px 0;
    }

</style>
