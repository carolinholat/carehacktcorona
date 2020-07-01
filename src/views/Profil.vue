<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <v-btn class="primary" style="margin: 20px" @click="aboverwaltung = !aboverwaltung">
                {{aboverwaltung ? 'ZURÜCK ZU PROFIL' : 'THEMENABOS VERWALTEN'}}
            </v-btn>
            <h2 align="center" class="spaced">Mein Profil</h2>
            <v-row>
                <v-col cols="10" class="mx-auto" v-if="!aboverwaltung">
                    <v-text-field
                            label="Vorname"
                            placeholder="Erika"
                            v-model="personVorname"
                    ></v-text-field>
                    <v-text-field
                            label="Nachname"
                            placeholder="Mustermann"
                            v-model="personName"
                    ></v-text-field>
                    <v-text-field
                            label="Mail"
                            placeholder="erika@pro-juventa.de"
                            v-model="personMail"
                    ></v-text-field>
                    <v-text-field
                            label="Mail privat"
                            placeholder="erika@web.de"
                            v-model="personPrivatMail"
                    ></v-text-field>
                    <v-text-field
                            label="Handynr"
                            placeholder="01761111111"
                            v-model="personHandy"
                    ></v-text-field>

                    <v-text-field
                            label="Telegram Chat-ID"
                            placeholder="1111111"
                            v-model="telegramId"
                    ></v-text-field>


                        <v-text-field
                                autocomplete="current-password"
                                :value="userPassword"
                                label="Passwort alt (bitte ausfüllen, wenn Sie ein neues Passwort vergeben wollen)"
                                :append-icon="value ? 'mdi-eye' : 'mdi-eye-off'"
                                @click:append="() => (value = !value)"
                                :type="value ? 'password' : 'text'"
                                @input="_=>userPassword=_"
                        ></v-text-field>
                    <v-text-field
                            autocomplete="current-password"
                            :value="userPasswordNew"
                            label="Passwort neu"
                            :append-icon="value ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="() => (valueNeu = !valueNeu)"
                            :type="valueNeu ? 'password' : 'text'"
                            @input="_=>userPasswordNew=_"
                    ></v-text-field>



                    <v-btn @click="save()">SPEICHERN</v-btn>
                </v-col>
                <v-col cols="10" class="mx-auto" v-else>
                    <div class="text-center spaced" v-if="loading">
                        <v-progress-circular
                                indeterminate
                                color="primary"
                        ></v-progress-circular>
                    </div>
                    <div v-if="!loading">
                        <h3 class="spaced">Abonnierte Themen</h3>
                        <h4 class="spaced">Über Abteilung abonniert</h4>
                        <div v-if="themenFix.length > 0" class="d-flex">
                            <v-btn class="primary" v-for="thema in themenFix" :key="thema.id">{{thema.name}}</v-btn>
                        </div>
                        <h6 v-else class="spaced">Keine Themen vorhanden</h6>
                        <h4 class="spaced">Zusätzlich abonniert</h4>
                        <div v-if="themenAbonniert.length > 0" class="d-flex">
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on }">
                                    <v-btn class="primary" v-for="thema in themenAbonniert"
                                           :key="thema.id" v-on="on"
                                           @click="desabonnieren(thema.id)">
                                        {{thema.name}}
                                    </v-btn>
                                </template>
                                <span>Nicht mehr abonnieren</span>
                            </v-tooltip>
                        </div>
                        <h6 v-else class="spaced">Keine Themen vorhanden</h6>
                        <h4 class="spaced">Weitere Themen</h4>
                        <div v-if="themenNichtAbonniert.length > 0" class="d-flex">
                            <v-tooltip bottom>
                                <template v-slot:activator="{ on }">
                                    <v-btn class="primary" v-for="thema in themenNichtAbonniert"
                                           :key="thema.id"
                                           v-on="on"
                                           @click="abonnieren(thema.id)">
                                        {{thema.name}}
                                    </v-btn>
                                </template>
                                <span>Abonnieren</span>
                            </v-tooltip>
                        </div>
                        <h6 v-else class="spaced">Keine Themen vorhanden</h6>
                    </div>
                </v-col>
            </v-row>
        </v-card>
        <WarningServerBug v-if="$store.state.serverBug"/>
    </div>
</template>

<script>
    import axios from "../plugins/axios";
    import WarningServerBug from '../../src/components/layout/WarningServerBug'

    export default {
        components: {
            WarningServerBug
        },
        data() {
            return {
                loading: false,
                aboverwaltung: false,
                itemKategorie: ['Frage', 'Abteilung', 'Person', 'Thema'],
                chosenItemKategorie: '',
                frageText: '',
                antwortText: '',
                abteilung: '',
                thema: '',
                personVorname: '',
                personName: '',
                personAbteilung: '',
                personMail: '',
                personPrivatMail: '',
                personHandy: '',
                tagThemenList: [],
                themenFix: [],
                themenAbonniert: [],
                themenNichtAbonniert: [],
                tagAbteilungenList: [],
                userPassword: "",
                //valid: true,
                value: true,
                valueNeu: true,
                userPasswordNew: "",
                telegramId: ''
            }
        },
        mounted() {
            let self = this;
            let url = this.$store.state.url;
            axios
                .post(url + '/api/init.php', 'themenundabteilungen')
                .then(response => self.initThemenUndAbteilungen(response.data))
                .catch(error => self.handleErrors(error));

            let postObj = {};
            postObj.action = 'getProfil';

            // wenn bereits registriert
            if (!this.$route.query.key ||  this.$route.query.key.length < 1) {

                postObj.token = this.$store.state.token;

                axios
                    .post(url + '/api/profil.php', postObj)
                    .then(response => self.initProfil(response.data))
                    .catch(error => self.handleErrors(error));
            }

            else {
                // erstregistrierung
                postObj.token = this.$route.query.key;
                postObj.id = this.$route.query.id;

                axios
                    .post(url + '/api/profil.php', postObj)
                    .then(response => self.initProfil(response.data))
                    .catch(error => self.handleErrors(error));
            }

        },
        methods: {
            handleErrors(error) {
                if (error /*.response.status < 200 || error.response.status > 299 */ ) {
                    this.$store.commit('setServerBug', true);
                }
            },
            initProfil(data) {
                if (data !== '405') {
                    this.personVorname = data.user.vorname;
                    this.personName = data.user.name;
                    this.personMail = data.user.mail;
                    this.personPrivatMail = data.user.mail_privat;
                    this.personHandy = data.user.handy;
                }
                else {
                    this.$router.push({ path: '/login' });
                }
            },
            initThemenUndAbteilungen(data) {
                this.tagThemenList = data.themen;
                this.tagAbteilungenList = data.abteilungen;
                this.orderThemen();
            },
            orderThemen() {
                for(let id of Object.keys(this.tagThemenList)) {
                    let thema = {};
                    thema.id = id;
                    thema.name = this.tagThemenList[id].name;
                    if(this.$store.state.aboFix.includes(id)) {
                        this.themenFix.push(thema);
                    } else if(this.$store.state.aboFlex.includes(id)) {
                        this.themenAbonniert.push(thema);
                    } else {
                        this.themenNichtAbonniert.push(thema);
                    }
                }
            },
            abonnieren(id) {
                let idInt = parseInt(id);
                let postObj = {};
                postObj.action = 'abonnieren';
                postObj.token = this.$store.state.token;
                postObj.id = idInt;
                let self = this;
                let url = this.$store.state.url;
                axios.post(url + '/api/profil.php', postObj)
                    .then(response => self.refreshProfil(response.data));

                let thema = this.themenNichtAbonniert.filter(thema => thema.id === id)[0];
                this.themenAbonniert.push(thema);
                this.themenNichtAbonniert = this.themenNichtAbonniert.filter(thema => thema.id !== id);

            },
            desabonnieren(id) {
                let idInt = parseInt(id);
                let postObj = {};
                postObj.action = 'desabonnieren';
                postObj.token = this.$store.state.token;
                postObj.id = idInt;
                let self = this;
                let url = this.$store.state.url;
                axios.post(url + '/api/profil.php', postObj)
                    .then(response => self.refreshProfil(response.data))
                    .catch(self.$store.commit('setServerBug', true) );

                let thema = this.themenAbonniert.filter(thema => thema.id === id)[0];
                this.themenNichtAbonniert.push(thema);
                this.themenAbonniert = this.themenAbonniert.filter(thema => thema.id !== id);
            },
            refreshProfil(data) {
                this.$store.commit('setAboFlex', data);

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
                let url = this.$store.state.url;
                axios
                    .post(url + '/api/new_items.php', postObj)
                    .then(response => console.log(response.data))
                    .catch(self.$store.commit('setServerBug', true) );
            }
        },
    };
</script>

<style>
    .cardCentered {
        width: 80%;
        margin: auto;
        min-height: 400px
    }

    .spaced {
        padding-top: 10px;
        padding-bottom: 15px
    }

</style>
