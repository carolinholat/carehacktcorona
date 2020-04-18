<template>


    <v-row class="main">
        <v-col cols="4" style="background-color: #e1f5fe;">
            <div class="sidebar">
                <h3>Übersicht</h3>
                <div>
                    <v-btn color="primary" class="infoBtn">Alle Beiträge ansehen</v-btn><br>
                    <v-btn color="primary" class="infoBtn">Fragen freigeben</v-btn><br>
                    <v-btn color="primary" class="infoBtn">FAQ - Beitrag erstellen</v-btn><br>
                    <v-btn color="primary" class="infoBtn">Lesestatistiken</v-btn><br>
                    <v-btn color="primary" class="infoBtn">Organigramm verwalten</v-btn><br>
                    <v-btn color="primary" class="infoBtn">Mitarbeiter anzeigen</v-btn><br>
                    <v-btn color="primary" class="infoBtn">Mitarbeiter anlegen</v-btn><br>
                </div>
            </div>
        </v-col>
        <v-col cols="8">
            <div class="sidebar">
                <h2 align="center" class="spaced">Neue Informationen anlegen</h2>
                <v-row>
                    <v-col cols="10" class="mx-auto">
                        <v-select
                                class="spaced"
                                align="center"
                                :items="itemKategorie"
                                v-model="chosenItemKategorie"
                                label="Was möchten Sie neu anlegen"
                        ></v-select>
                        <v-text-field
                                v-if="chosenItemKategorie === 'Frage'"
                                label="Frage"
                                placeholder="Ihr Fragetext"
                                v-model="frageText"
                        ></v-text-field>
                        <v-text-field
                                v-if="chosenItemKategorie === 'Frage'"
                                label="Antwort"
                                placeholder="Ihr Antworttext"
                                v-model="antwortText"
                        ></v-text-field>

                        <v-text-field
                                v-if="chosenItemKategorie === 'Abteilung'"
                                label="Abteilung"
                                placeholder="Abteilungsname"
                                v-model="abteilung"
                        ></v-text-field>
                        <v-text-field
                                v-if="chosenItemKategorie === 'Thema'"
                                label="Themenfeld"
                                placeholder="Themenfeldbezeichnung"
                                v-model="thema"
                        ></v-text-field>


                        <v-text-field
                                v-if="chosenItemKategorie === 'Person'"
                                label="Person"
                                placeholder="Vor- und Nachname Person"
                                v-model="personName"
                        ></v-text-field>
                        <v-text-field
                                v-if="chosenItemKategorie === 'Person'"
                                label="Mail"
                                placeholder="Mailadresse Person"
                                v-model="personMail"
                        ></v-text-field>
                        <v-text-field
                                v-if="chosenItemKategorie === 'Person'"
                                label="Abteilung der Person"
                                placeholder="Abteilungsname"
                                v-model="personAbteilung"
                        ></v-text-field>

                        <v-btn @click="save()">SPEICHERN</v-btn>

                    </v-col>
                </v-row>
            </div>
        </v-col>
    </v-row>

</template>

<script>
    import axios from "../plugins/axios";

    export default {
        components: {},
        mounted() {
            let self = this;
            axios
                .post('http://localhost:8000/carehacktcorona/api/init.php', 'themenundabteilungen')
                .then(response => self.initThemenUndAbteilungen(response.data));
        },
        methods: {
            initThemenUndAbteilungen(data) {
                this.tagThemenList = data.themen;
                this.tagAbteilungenList = data.abteilungen;
            },
            save() {
                let postObj = {};
                postObj.typ = this.chosenItemKategorie.toLowerCase();
                if(this.chosenItemKategorie === 'Frage') {
                    postObj.frage = this.frageText;
                    postObj.antwort = this.antwortText;
                } else if(this.chosenItemKategorie === 'Abteilung') {
                    postObj.abteilung = this.abteilung;
                } else if(this.chosenItemKategorie === 'Thema') {
                    postObj.thema = this.thema;
                } else {
                    postObj.name = this.personName;
                    postObj.mail = this.personMail;
                    postObj.abteilung = this.personAbteilung;
                }
                this.chosenItemKategorie = '';
                this.frageText = '';
                this.antwortText = '';
                this.abteilung = '';
                this.thema = '';
                this.personName = '';
                this.personMail = '';
                this.personAbteilung = '';
                console.log(this.thema + 'TEST');
                axios
                    .post('http://localhost:8000/carehacktcorona/api/new_items.php', postObj)
                    .then(response => console.log(response.data));
            }
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

    .infoBtn {
        margin: 20px 0;
        width: 250px;
    }

    .spaced {
        margin: 10px 0;
    }

</style>
