<template>


    <v-row class="main">
        <v-col cols="4" style="background-color: #e1f5fe;">
            <div class="sidebar">
                <h3>Übersicht</h3>
                <div>
                    <!--<v-btn color="primary" class="infoBtn" @click="status='Beiträge'">Alle Beiträge ansehen</v-btn>
                    <br> -->
                    <v-btn color="primary" class="infoBtn" @click="status='Fragen'">Fragen freigeben</v-btn>
                    <br>
                    <v-btn color="primary" class="infoBtn" @click="status='FAQ'">FAQ - Beitrag erstellen</v-btn>
                    <br>
                    <v-btn color="primary" class="infoBtn" @click="status='Thema'">Thema erstellen</v-btn>
                    <br>
                    <v-btn color="primary" class="infoBtn" @click="status='Lesestatistiken'">Lesestatistiken</v-btn>
                    <br>
                    <v-btn color="primary" class="infoBtn" @click="status='Organigramm'">Organigramm verwalten</v-btn>
                    <br>
                    <v-btn color="primary" class="infoBtn" @click="status='Mitarbeiter'">Mitarbeiter</v-btn>
                    <br>
                </div>
            </div>
        </v-col>
        <v-col cols="8">
            <div class="sidebar">
                <h2 align="center" class="spaced">Dashboard - {{status}}</h2>
                <v-row>
                    <v-col cols="10" class="mx-auto">

                        <FAQ v-if="status === 'FAQ'"
                             :abteilungenListe="listObjectToArray(abteilungenListe)"
                             :kategorienListe="listObjectToArray(kategorienListe)"
                             :themenListe="listObjectToArray(themenListe)"
                             @loadFragen="setUser()"/>

                        <Organigramm v-if="status === 'Organigramm'"
                                     :abteilungenListe="listObjectToArray(abteilungenListe)"
                                     :kategorienListe="listObjectToArray(kategorienListe)"
                                     :themenListe="listObjectToArray(themenListe)"/>

                        <!-- <Beitraege v-if="status === 'Beitraege'" :beitraege="beitraege"/> -->

                        <!-- <Lesestatistiken v-if="status === 'Lesestatistiken'" :leseStatistiken="leseStatistiken"/> -->

                        <Thema v-if="status === 'Thema'"
                               :abteilungenListe="listObjectToArray(abteilungenListe)"
                               :kategorienListe="listObjectToArray(kategorienListe)"
                               :themenListe="listObjectToArray(themenListe)"/>

                        <Fragen v-if="status === 'Fragen'"
                                :fragen="fragenZuBeantworten"
                                :abteilungenListe="listObjectToArray(abteilungenListe)"
                                :kategorienListe="listObjectToArray(kategorienListe)"
                                :themenListe="listObjectToArray(themenListe)"
                                @loadFragen="setUser()"/>

                        <Mitarbeiter v-if="status === 'Mitarbeiter'"
                                     :user="user"
                                     :abteilungen="abteilungenListe"
                                     :kategorien="kategorienListe"
                                     :abteilungenListe="listObjectToArray(abteilungenListe)"
                                     @loadUser="setUser()"/>

                    </v-col>
                </v-row>
            </div>
        </v-col>
    </v-row>

</template>

<script>
    import axios from "../plugins/axios";
    import Beitraege from "./../components/Utils/Dashboard/Beitraege";
    import FAQ from "./../components/Utils/Dashboard/FAQ";
    import Fragen from "./../components/Utils/Dashboard/Fragen";
    import Lesestatistiken from "./../components/Utils/Dashboard/Lesestatistiken";
    import Mitarbeiter from "./../components/Utils/Dashboard/Mitarbeiter";
    import Organigramm from "./../components/Utils/Dashboard/Organigramm";
    import Thema from "./../components/Utils/Dashboard/Thema";


    export default {
        components: {Mitarbeiter, FAQ, Fragen, Beitraege, Lesestatistiken, Organigramm, Thema},
        data() {
            return {
                status: 'FAQ',
                themenListe: [],
                abteilungenListe: [],
                kategorienListe: [],
                user: [],
                beitraege: [],
                fragenZuBeantworten: [],
                leseStatistiken: []
            }
        },
        mounted() {
            let self = this;
            axios
                .post('http://localhost:8000/api/init.php', 'themenundabteilungen')
                .then(response => self.initThemenUndAbteilungen(response.data));

            this.setUser();
        },
        methods: {
            setUser() {
                let self = this;
                let login = this.$store.state.token;
                axios
                    .post('http://localhost:8000/api/superuser.php', login)
                    .then(response => self.initFragenUndUser(response.data));
            },
            initThemenUndAbteilungen(data) {
                this.themenListe = data.themen;
                this.abteilungenListe = data.abteilungen;
                this.kategorienListe = data.kategorien;
            },
            initFragenUndUser(data) {
                this.user = data.user;
                this.beitraege = data.beitraege;
                this.fragenZuBeantworten = data.fragen_zu_beantworten;
            },

            listObjectToArray(obj) {
                let array = [];
                let objectValues = Object.values(obj);
                for(let obj of objectValues) {
                    let item = {};
                    item.value = objectValues.indexOf(obj) + 1;
                    item.text = obj.name;
                    array.push(item);
                }
                return array;
            }

            /*save() {
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
            } */
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
