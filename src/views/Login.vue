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
                let postObj = {};
                postObj.mail = this.mail;
                postObj.pw = this.pw;
                let self = this;
                axios.post('http://localhost:8000/carehacktcorona/api/login.php', postObj)
                    .then(response => console.log(response.data));
            }
        },
        data() {
            return {
                mail: '',
                pw: ''
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
