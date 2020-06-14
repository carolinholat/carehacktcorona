<template>
    <div>
        <v-card grid-list-md text-xs-center style="width: 40%; margin: auto; margin-top: 150px">
            <v-card-title
                    class="headline grey lighten-2"
                    primary-title
            >
                LOGIN
            </v-card-title>

            <v-card-text align="center" style="width: 80%; margin: auto">
                <v-text-field
                        label="Email-Adresse"
                        placeholder="user@beispiel.de"
                        v-model="mail"
                ></v-text-field>
                <v-text-field
                        label="Passwort"
                        placeholder="demo"
                        v-model="pw"
                ></v-text-field>
                <p class="warning" v-if="unauthorized">Die Daten sind falsch</p>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                        color="primary"
                        text
                        @click="login()"
                >
                    Anmelden
                </v-btn>
            </v-card-actions>
        </v-card>

    </div>
</template>

<script>
    import axios from "../plugins/axios";

    export default {
        components: {},
        methods: {
            login() {
                let url = this.$store.state.url;
                let postObj = {};
                postObj.mail = this.mail;
                postObj.pw = this.pw;
                let self = this;
                axios.post(url + '/api/login.php', postObj)
                    .then(response => self.setToken(response.data));
            },
            setToken(responseData) {
                if (responseData.token !== '405') {

                    this.$store.commit('setToken', responseData.token);
                    this.$store.commit('setAbo', responseData.abo);
                    this.$store.commit('setAboFix', responseData.abo_pflicht);
                    this.$store.commit('setAboFlex', responseData.abo_flex);
                    this.$store.commit('setAbteilung', responseData.abteilung);
                    this.$store.commit('setKategorien', responseData.kategorien);

                    if (this.$store.state.token !== "") {
                        this.$router.push({ path: '/infos' });
                    }
                    if (responseData.role === 'admin') {
                        this.$store.commit('setAdmin');
                    }
                }
                else {
                    this.unauthorized = true;
                }
            }

        },
        data() {
            return {
                mail: '',
                pw: '',
                unauthorized: false
            }
        }
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
