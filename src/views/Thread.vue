<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <v-btn color="primary" style="margin: 20px">ZUR FORUMSÜBERSICHT</v-btn>
            <h2 align="center" class="spaced">{{title}}</h2>
            <v-row>
                <v-col cols="10" class="mx-auto">
                    <p>Erstellt: {{erstellt}}</p>
                    <div style="margin-top: 30px">
                        <p>Erika schrieb am 1.1.2020:</p>
                        <v-card>
                            <p style="padding: 20px">Ich bin mir unsicher, was alles wie desinfiziert werden muss. Bitte um genauere Erläuterung!</p>
                        </v-card>
                    </div>
                    <div v-if="!archiv">
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
                        <v-btn>SENDEN</v-btn>
                    </div>

                    <v-btn
                            v-if="!archiv"
                            @click="close()" style="margin-top: 30px">THREAD BEENDEN</v-btn>
                    <v-btn
                            v-if="archiv"
                            @click="open()" style="margin-top: 30px">THREAD WIEDER ÖFFNEN</v-btn>


                </v-col>
            </v-row>
        </v-card>

    </div>
</template>

<script>
    import axios from "../plugins/axios";

    export default {
        components: {},
        data() {
            return {
                title: 'Forumsfragetitel',
                erstellt: 'Montag',
                archiv: false,
                antwortText: ''
            }
        },
        mounted() {
        },
        methods: {

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
