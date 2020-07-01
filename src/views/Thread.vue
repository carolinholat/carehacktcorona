<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <v-btn class="primary" style="margin: 20px">
                <router-link to="/forum" class="routerLink">ZUR FORUMSÜBERSICHT</router-link>
            </v-btn>

            <div class="text-center spaced" v-if="loading">
                <v-progress-circular
                        indeterminate
                        color="primary"
                ></v-progress-circular>
            </div>

            <h2 align="center" class="spaced" v-if="!loading">{{frage.frage}}</h2>
            <v-row v-if="!loading">
                <v-col cols="10" class="mx-auto">
                    <p>Erstellt: {{frage.zeitpunkt_erstellung}}</p>
                    <div style="margin-top: 30px" v-if="thread.length > 0">
                        <div v-for="beitrag in thread" :key="beitrag.zeitstempel">
                            <p>{{beitrag.vorname}} {{beitrag.name}} schrieb am {{beitrag.zeitstempel}}:</p>
                            <v-card>
                                <p style="padding: 20px">{{beitrag.text}}</p>
                            </v-card>
                        </div>
                    </div>
                    <div v-else>Keine Beiträge in diesem Thread</div>
                    <div v-if="!archiv && $store.state.token !== ''">
                        <p>
                            Ihre Antwort:
                        </p>
                        <v-card>
                            <p style="padding: 20px">
                                <v-text-field
                                        label="Antwort"
                                        placeholder="Ihr Antworttext"
                                        v-model="antwortText"
                                ></v-text-field>
                            </p>
                        </v-card>
                        <v-btn v-if="$store.state.token !== ''"
                                @click="saveBeitrag()">SENDEN</v-btn>
                        <WarningOverlay
                                v-if="overlay"
                                @weiter="overlay = false"
                                :msg="'Der Text darf nicht leer sein. Klicken Sie, um dieses Fenster zu schließen'"/>
                    </div>

                    <v-btn
                            v-if="!archiv && $store.state.token !== ''"
                            @click="close()" style="margin-top: 30px">THREAD BEENDEN
                    </v-btn>
                    <v-btn
                            v-if="archiv && $store.state.token !== ''"
                            @click="open()" style="margin-top: 30px">THREAD WIEDER ÖFFNEN
                    </v-btn>


                </v-col>
            </v-row>
        </v-card>
        <WarningServerBug v-if="$store.state.serverBug"/>
    </div>
</template>

<script>
    import axios from "../plugins/axios";
    import WarningOverlay from "../../src/components/layout/WarningOverlay"
    import WarningServerBug from '../../src/components/layout/WarningServerBug'

    export default {
        components: {
            WarningOverlay,
            WarningServerBug
        },
        data() {
            return {
                title: 'Forumsfragetitel',
                erstellt: 'Montag',
                archiv: false,
                antwortText: '',
                loading: true,
                threadId: 0,
                frage: null,
                thread: null,
                overlay: false
            }
        },
        mounted() {
            if(this.$route.query.id) {
                this.threadId = parseInt(this.$route.query.id);
            }
            let postObj = {};
            postObj.token = this.$store.state.token;
            postObj.id = this.threadId;
            let self = this;
            let url = this.$store.state.url;
            axios
                .post(url + '/api/thread_items.php', postObj)
                .then(response => self.initThread(response.data))
                .catch(error => self.handleErrors(error));
        },
        methods: {
            handleErrors(error) {
                if (error /*.response.status < 200 || error.response.status > 299 */ ) {
                    this.$store.commit('setServerBug', true);
                }
            },
            initThread(data) {
                this.frage = data.frage[0];
                this.thread = data.beitraege;
                this.loading = false;
            },
            saveBeitrag(){
                let postObj = {};
                postObj.action = 'insertThreadBeitrag';
                postObj.thread_ID = this.threadId;
                postObj.text = this.antwortText;
                postObj.token = this.$store.state.token;
                if (this.antwortText === '') {
                   this.overlay = true;
                }
                else {
                    let self = this;
                    let url = this.$store.state.url;
                    axios
                        .post(url + '/api/new_items.php', postObj)
                        .then(response => self.antwortText = '')
                        .catch(self.$store.commit('setServerBug', true) );
                }
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

    .routerLink {
        text-decoration: none;
        color: whitesmoke;
    }

    .routerLink:hover {
        text-decoration: none;
        color: yellow;
    }

</style>
