<template>


    <v-row class="main">
        <v-col cols="12" lg="3" class="blue">
            <div class="sidebar">
                <h3>Geschäftsbereiche</h3>

                <v-switch
                        v-if="$store.state.token !== '' && abteilungenListe.length > 0 && kategorienListe.length > 0 && themenListe.length > 0"
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
        <v-col  cols="12" lg="6" >
            <div class="sidebar">
                <h2 align="center" class="spaced">Übersicht Forums-Threads</h2>
                <p style="height: 30px"></p>
                <div v-if="(nurAktive && fragenResFilterAktiv.length > 0) || (!nurAktive && fragenResFilterAlle.length > 0)">
                    <v-data-table
                            :headers="threadHeaders"
                            :items="nurAktive ? fragenResFilterAktiv : fragenResFilterAlle"
                            :items-per-page="5"
                            class="elevation-1">
                        <template v-slot:body="{ items }">
                            <tr v-for="item in items" :key="item.id">
                                <td v-for="(header, index) of threadHeaders" :key="header.value + index">
                                    <template v-if="header.value !== 'forum_thread'">
                                        {{item[header.value]}}
                                    </template>
                                    <template v-else-if="header.value === 'forum_thread'">
                                        <v-btn text :href="item[header.value]" target="_blank" class="info">
                                            <router-link :to="'/thread?id=' + item.forum_thread" class="routerLink">WEITER</router-link>
                                        </v-btn>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </div>
                <div v-else>{{ nurAktive ? 'Keine offenen Forumsthreads vorhanden' : 'Keine Forumsthreads vorhanden'}}</div>
            </div>
        </v-col>
        <v-col cols="12" lg="3" style=""
               :class="blue">

            <v-card class="sidebar" style="min-height: 50px">
                <div class="cardContent">
                    <NewsTicker :fragen="threadItemsActive" @selectFrage="jumpToMeldung($event.ID)"/>
                </div>
            </v-card>
            <v-card class="sidebar" style="min-height: 50px; margin-top: 30px">
                <div class="cardContent">
                    <h3>Kontakt</h3>
                </div>
            </v-card>
        </v-col>
        <WarningServerBug v-if="$store.state.serverBug"/>
    </v-row>
</template>

<script>
    import axios from "../plugins/axios";
    import NewsTicker from './../components/Utils/NewsTicker';
    import WarningServerBug from '../../src/components/layout/WarningServerBug'


    export default {
        components: {
            NewsTicker,
            WarningServerBug
        },
        mounted() {
            let self = this;
            let url = this.$store.state.url;
            axios
                .post(url + '/api/filter_fragen.php', this.$store.state.token)
                .then(response => self.initFragen(response.data))
                .catch(error => self.handleErrors(error));

            axios
                .post(url + '/api/init.php', 'themenundabteilungen')
                .then(response => self.initThemenUndAbteilungen(response.data))
                .catch(error => self.handleErrors(error));

            axios
                .post(url + '/api/filter_forum.php', this.$store.state.token)
                .then(response => self.initForum(response.data))
                .catch(error => self.handleErrors(error));
        },
        methods: {
            handleErrors(error) {
                if (error /*.response.status < 200 || error.response.status > 299 */ ) {
                    this.$store.commit('setServerBug', true);
                }
            },
            jumpToMeldung(id) {
                this.$router.push({ path: '/infos?id=' + id });
            },
            initForum(data) {
                let aktiv = data.forum_aktiv;
                this.threadItemsActive = aktiv;
                let alle = data.forum_alle;
                this.threadItems = alle;
                this.fragenResFilterAlle = this.threadItems;
                this.fragenResFilterAktiv = this.threadItemsActive;

            },
            initFragen(data) {
                this.fragenVonAbteilung = data.frage_von_abteilung;
                this.fragenVonKategorie = data.frage_von_kategorie;
                this.fragenVonThema = data.frage_von_thema;
            },

            initThemenUndAbteilungen(data) {
                try {
                    this.themenListe = this.listObjectToArray(data.themen, 'themen');
                    this.abteilungenListe = this.listObjectToArray(data.abteilungen, 'abteilungen');
                    this.kategorienListe = this.listObjectToArray(data.kategorien, 'kategorien');
                } catch(Exception) {
                    // wenn die datenbank leer ist
                    this.themenListe = [];
                    this.abteilungenListe = [];
                    this.kategorienListe = [];
                }

            },

            filteredFragen() {
                let array = this.threadItems;
                let arrayActive = this.threadItemsActive;
                if(this.fragenVonAbteilung.length > 0) {
                    let id_array = [];
                    for(let frage of this.fragenVonAbteilung) {
                        if(this.filterAbteilungenListe.includes(parseInt(frage['abteilung_id']))) {
                            id_array.push(frage['frage_ID']);
                        }
                    }
                    array = array.filter(frage => id_array.includes(frage['ID']));
                    arrayActive = arrayActive.filter(frage => id_array.includes(frage['ID']));
                }

                if(this.fragenVonKategorie.length > 0) {
                    let id_array = [];
                    for(let frage of this.fragenVonKategorie) {
                        if(this.filterKategorienListe.includes(parseInt(frage['kategorie_id']))) {
                            id_array.push(frage['frage_ID']);
                        }
                    }
                    array = array.filter(frage => id_array.includes(frage['ID']));
                    arrayActive = arrayActive.filter(frage => id_array.includes(frage['ID']));
                }

                if(this.fragenVonThema.length > 0) {
                    let id_array = [];
                    for(let frage of this.fragenVonThema) {
                        if(this.filterThemenListe.includes(parseInt(frage['thema_id']))) {
                            id_array.push(frage['frage_ID']);
                        }
                    }
                    array = array.filter(frage => id_array.includes(frage['ID']));
                    arrayActive = arrayActive.filter(frage => id_array.includes(frage['ID']));
                }
                this.fragenResFilterAlle = array;
                this.fragenResFilterAktiv = arrayActive;

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
                if (!this.filterThemen && !this.filterBereiche && !this.filterAbteilungen) {
                    this.fragenResFilterAlle = this.threadItems;
                    this.fragenResFilterAktiv = this.threadItemsActive;
                }
                else {
                    this.filteredFragen();
                }
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
                fragenResFilterAlle: [],
                fragenResFilterAktiv: [],
                fragenVonAbteilung: [],
                fragenVonThema: [],
                fragenVonKategorie: [],
                einzelneMeldung: 0,
                nurAktive: false,
                threadHeaders: [{text: 'Fragetext', value: 'frage'},
                    {text: 'Erstellt am', value: 'zeitpunkt_erstellung'}, {text: 'Zuletzt beantwortet am', value: 'zeitstempel'}, {text: 'zum Thread', value: 'forum_thread'}],
                threadItems: [{id: 'Meine Frage', text: 'Meine Frage', zeitpunkt_erstellung: 'gestern', zeitstempel: 'heute', forum_thread: 'test'}],
                threadItemsActive : [{id: 'Meine Frage', text: 'Meine Frage', zeitpunkt_erstellung: 'gestern', zeitstempel: 'heute', forum_thread: 'test'}]
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
