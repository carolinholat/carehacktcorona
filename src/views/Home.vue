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
                        <div v-for="frage in fragenArray" :key="frage.ID">
                            <h4>{{frage.frage}}</h4>
                            <p class=".font-italic font-weight-light">Frage am: {{frage.zeitpunkt_erstellung}}</p>
                            <v-card class="cardContent">{{frage.antwort}}</v-card>
                            <p class=".font-italic font-weight-light text-right">Antwort am: {{frage.zeitpunkt_antwort}}</p>
                            <p style="height: 20px"></p>
                            <v-btn class="spaced" v-if="frage.forum_thread !== null">Zum Forum</v-btn>
                            <v-btn class="spaced" v-else>Forum Thread eröffnen</v-btn>
                        </div>
                    </div>
                </v-col>
                <v-col cols="3" >
                    <NewsTicker :fragen="fragenArray"/>
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
    import NewsTicker from './../components/Utils/NewsTicker';

    export default {
        components: {
            NewsTicker
        },
        mounted() {
            let self = this;
            axios
                .get('http://localhost:8000/api/filter_fragen.php')
                .then(response => self.initFrageFilter(response.data));

            axios
                .post('http://localhost:8000/api/init.php', 'themenundabteilungen')
                .then(response => self.initThemenUndAbteilungen(response.data));
        },
        methods: {
            initFrageFilter(responseData) {
                this.fragenArray = responseData.fragen;
                this.frageVonAbteilung = responseData.frage_von_abteilung;
                this.frageVonKategorie = responseData.frage_von_kategorie;
                this.frageVonThema = responseData.frage_von_thema;
            },
            initThemenUndAbteilungen(data) {
                this.themenListe = data.themen;
                this.abteilungenListe = data.abteilungen;
                this.kategorienListe = data.kategorien;
            },
        },
        data() {
            return {
                abteilungOderKategorie: 'abteilung',
                thema: '',
                fragenArray: [{ID: 1, frage: 'Müssen wir Mundschutz tragen?', antwort: 'ja', zeitpunkt_erstellung: '2020-04-22 10:08:04', zeitpunkt_antwort: '2020-04-22 10:08:04', forum_thread: null}],
                frageVonKategorie: [],
                frageVonAbteilung: [],
                frageVonThema: [],
                themenListe: [],
                abteilungenListe: [],
                kategorienListe: []
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
