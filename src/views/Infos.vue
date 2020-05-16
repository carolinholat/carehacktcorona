<template>


    <v-row class="main">
        <v-col cols="2" style="background-color: #e1f5fe;">
            <div class="sidebar">
                <h3>Geschäftsbereiche</h3>

                <v-switch
                        v-if="$store.state.token !== ''"
                        @change="modifyFilterPersonalisiert()"
                        v-model="filterPersonalisiert" label="Personalisierten Filter anwenden"></v-switch>

                <v-switch v-model="filterAbteilungen" label="Nach Abteilung filtern?"
                          @change="closeFilter('abteilungen')"></v-switch>
                <div v-if="filterAbteilungen">
                    <v-checkbox v-for="abteilung in abteilungenListe"
                                :key="abteilung.value" :label="abteilung.text"
                                :value="abteilung.value"
                                v-model="filterAbteilungenListe"
                                @change="filteredFragen()"></v-checkbox>

                </div>

                <v-switch v-model="filterBereiche" label="Nach Bereich filtern?"
                          @change="closeFilter('kategorien')"></v-switch>
                <div v-if="filterBereiche">
                    <v-checkbox v-for="bereich in kategorienListe"
                                :key="bereich.value" :label="bereich.text"
                                :value="bereich.value"
                                v-model="filterKategorienListe"
                                @change="filteredFragen()"></v-checkbox>
                </div>

                <v-switch v-model="filterThemen" label="Nach Themen filtern?"
                          @change="closeFilter('themen')"></v-switch>
                <div v-if="filterThemen">
                    <v-checkbox v-for="thema in themenListe"
                                :value="thema.value"
                                v-model="filterThemenListe"
                                :key="thema.value"
                                :label="thema.name"
                                @change="filteredFragen()"></v-checkbox>
                </div>

            </div>
        </v-col>
        <v-col cols="7">

            <div class="sidebar" v-if="einzelneMeldung < 1">

                <h2 align="center" class="spaced">Übersicht Info-Meldungen</h2>
                <p style="height: 30px"></p>
                <div v-for="frage in fragenResFilter" :key="frage.ID">
                    <h4>{{frage.frage}}</h4>
                    <p class=".font-italic font-weight-light">Frage am: {{frage.zeitpunkt_erstellung}}</p>
                    <p class=".font-italic font-weight-light">Antwort am: {{frage.zeitpunkt_antwort}}</p>
                    <v-card class="cardContent">{{frage.antwort}}</v-card>
                    <v-btn class="spaced" v-if="frage.forum_thread !== null">Zum Forum</v-btn>
                    <v-btn class="spaced" style="margin-top: 20px" v-else>Forum-Thread starten</v-btn>
                </div>

            </div>
            <div class="sidebar" v-else>
                <v-btn text class="info" @click="einzelneMeldung = 0">ZURÜCK ZUR ÜBERSICHT</v-btn>
                <h2 align="center" class="spaced">Info-Meldung</h2>
                <p style="height: 30px"></p>

                <h4>{{fragen.filter(frage => frage.ID === einzelneMeldung)[0].frage}}</h4>
                <p class=".font-italic font-weight-light">Frage am: {{fragen.filter(frage => frage.ID ===
                    einzelneMeldung)[0].zeitpunkt_erstellung}}</p>
                <p class=".font-italic font-weight-light">Antwort am: {{fragen.filter(frage => frage.ID ===
                    einzelneMeldung)[0].zeitpunkt_antwort}}</p>
                <v-card class="cardContent">{{fragen.filter(frage => frage.ID === einzelneMeldung)[0].antwort}}</v-card>
                <v-btn class="spaced"
                       v-if="fragen.filter(frage => frage.ID === einzelneMeldung)[0].forum_thread !== null">Zum Forum
                </v-btn>
                <v-btn class="spaced" style="margin-top: 20px" v-else>Forum-Thread starten</v-btn>
            </div>


        </v-col>
        <v-col cols="3">

            <v-card class="sidebar" style="min-height: 50px">
                <div class="cardContent">
                    <NewsTicker :fragen="fragen" @selectFrage="einzelneMeldung = $event.ID"/>
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
    import NewsTicker from './../components/Utils/NewsTicker';

    export default {
        components: {
            NewsTicker
        },
        mounted() {
            let self = this;
            axios
                .post('http://localhost:8000/api/filter_fragen.php', this.$store.state.token)
                .then(response => self.initFragen(response.data));

            axios
                .post('http://localhost:8000/api/init.php', 'themenundabteilungen')
                .then(response => self.initThemenUndAbteilungen(response.data));
        },
        methods: {
            initFragen(data) {
                this.fragen = data.fragen;
                this.fragenResFilter = data.fragen; // erstmal keinen Filter, sondern alle übernehmen
                this.fragenVonAbteilung = data.frage_von_abteilung;
                this.fragenVonKategorie = data.frage_von_kategorie;
                this.fragenVonThema = data.frage_von_thema;
            },

            initThemenUndAbteilungen(data) {
                this.themenListe = this.listObjectToArray(data.themen, 'themen');
                this.abteilungenListe = this.listObjectToArray(data.abteilungen, 'abteilungen');
                this.kategorienListe = this.listObjectToArray(data.kategorien, 'kategorien');
            },

            listObjectToArray(obj, itemIdentifier) {
                let array = [];
                let filterArray = [];
                let objectValues = Object.values(obj);
                for(let obj of objectValues) {
                    let item = {};
                    item.value = objectValues.indexOf(obj) + 1;
                    filterArray.push(item.value);
                    item.text = obj.name;
                    array.push(item);
                }

                switch(itemIdentifier) {
                    case 'themen':
                        this.filterThemenListe = filterArray;
                        break;
                    case 'abteilungen':
                        this.filterAbteilungenListe = filterArray;
                        break;
                    case 'kategorien':
                        this.filterKategorienListe = filterArray;
                        break;
                }
                return array;
            },

            filteredFragen() {
                let array = this.fragen;
                if(this.fragenVonAbteilung.length > 0) {
                    let id_array = [];
                    for(let frage of this.fragenVonAbteilung) {
                        if(this.filterAbteilungenListe.includes(parseInt(frage['abteilung_id']))) {
                            id_array.push(frage['frage_ID']);
                        }
                    }
                    array = array.filter(frage => id_array.includes(frage['ID']));
                }

                if(this.fragenVonKategorie.length > 0) {
                    let id_array = [];
                    for(let frage of this.fragenVonKategorie) {
                        if(this.filterKategorienListe.includes(parseInt(frage['kategorie_id']))) {
                            id_array.push(frage['frage_ID']);
                        }
                    }
                    array = array.filter(frage => id_array.includes(frage['ID']));
                }

                if(this.fragenVonThema.length > 0) {
                    let id_array = [];
                    for(let frage of this.fragenVonThema) {
                        if(this.filterThemenListe.includes(parseInt(frage['thema_id']))) {
                            id_array.push(frage['frage_ID']);
                        }
                    }
                    array = array.filter(frage => id_array.includes(frage['ID']));
                }
                this.fragenResFilter = array;
            },
            modifyFilterPersonalisiert() {
                if(this.filterPersonalisiert) {
                    // filter setzen
                    this.filterAbteilungenListe = [parseInt(this.$store.state.abteilung)];
                    this.filterThemenListe = this.$store.state.abo;
                    this.filterKategorienListe = this.$store.state.kategorien;

                    // alle suchfelder öffnen
                    this.filterBereiche = true;
                    this.filterThemen = true;
                    this.filterAbteilungen = true;
                }
                this.filteredFragen();
            },
            closeFilter(art) {
                let result = [];
                switch(art) {
                    case 'abteilungen':
                        for (let abteilung of this.abteilungenListe) {
                            result.push(abteilung.value);
                        }
                        this.filterAbteilungenListe = result;
                        break;
                    case 'kategorien':
                        for (let kategorie of this.kategorienListe) {
                            result.push(kategorie.value);
                        }
                        this.filterKategorienListe = result;
                        break;
                    case 'themen':
                        for (let thema of this.themenListe) {
                            result.push(thema.value);
                        }
                        this.filterThemenListe = result;
                        break;
                }
                this.filteredFragen();
            },

        },
        computed: {},
        data() {
            return {
                filterPersonalisiert: false,
                step: 1,
                abteilungOderKategorie: 'abteilung',
                thema: '',
                themenListe: [],
                abteilungenListe: [],
                kategorienListe: [],
                filterAbteilungen: false,
                filterBereiche: false,
                filterThemen: false,
                filterAbteilungenListe: [],
                filterThemenListe: [],
                filterKategorienListe: [],
                fragen: [],
                fragenResFilter: [],
                fragenVonAbteilung: [],
                fragenVonThema: [],
                fragenVonKategorie: [],
                einzelneMeldung: 0,
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
