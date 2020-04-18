<template>


    <v-row class="main">
        <v-col cols="2" style="background-color: #e1f5fe;">
            <div class="sidebar">
                <h3>Geschäftsbereiche</h3>
                <v-switch v-model="nurAktive" label="Nur aktive Threads anzeigen?"></v-switch>
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


                <h2 align="center" class="spaced">Übersicht Forums-Threads</h2>
                <p style="height: 30px"></p>
                <div>
                    <v-data-table
                            :headers="threadHeaders"
                            :items="threadItems"
                            :items-per-page="5"
                            class="elevation-1">
                        <template v-slot:body="{ items }">
                            <tr v-for="item in items" :key="item.id">
                                <td v-for="(header, index) of threadHeaders" :key="header.value + index">
                                    <template v-if="header.value !== 'link'">
                                        {{item[header.value]}}
                                    </template>
                                    <template v-else-if="header.value === 'link'">
                                        <v-btn :href="item[header.value]" target="_blank" class="info">
                                            WEITER
                                        </v-btn>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
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
                thema: '',
                nurAktive: false,
                threadHeaders: [{text: 'ID', value: 'id'}, {text: 'Fragetext', value: 'text'},
                    {text: 'Erstellt am', value: 'created'}, {text: 'Zuletzt beantwortet am', value: 'answered'}, {text: 'zum Thread', value: 'link'}],
                threadItems: [{id: 1, text: 'Meine Frage', created: 'heute', answered: 'gestern', link: '/test'}]
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
