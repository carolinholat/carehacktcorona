<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <h2 align="center" class="spaced">Mein Profil</h2>
            <v-row>
                <v-col cols="10" class="mx-auto">
                        <v-text-field
                                label="Vorname"
                                placeholder="Erika"
                                v-model="vorname"
                        ></v-text-field>
                    <v-text-field
                            label="Nachname"
                            placeholder="Mustermann"
                            v-model="nachname"
                    ></v-text-field>
                    <v-text-field
                            label="Mail"
                            placeholder="erika@pro-juventa.de"
                            v-model="mail"
                    ></v-text-field>
                    <v-text-field
                            label="Mail"
                            placeholder="erika@pro-juventa.de"
                            v-model="mail"
                    ></v-text-field>
                    <v-text-field
                            label="Handynr"
                            placeholder="01761111111"
                            v-model="handy"
                    ></v-text-field>
                    <v-text-field
                            label="Passwort"
                            placeholder="01761111111"
                            v-model="handy"
                    ></v-text-field>

                    <v-btn @click="save()">SPEICHERN</v-btn>

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
                itemKategorie: ['Frage', 'Abteilung', 'Person', 'Thema'],
                chosenItemKategorie: '',
                frageText: '',
                antwortText: '',
                abteilung: '',
                thema: '',
                personName: '',
                personAbteilung: '',
                personMail: '',
                tagThemenList: [],
                tagAbteilungenList: []
            }
        },
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
                if (this.chosenItemKategorie === 'Frage') {
                    postObj.frage = this.frageText;
                    postObj.antwort = this.antwortText;
                }
                else if (this.chosenItemKategorie === 'Abteilung'){
                    postObj.abteilung = this.abteilung;
                }
                else if (this.chosenItemKategorie === 'Thema'){
                    postObj.thema = this.thema;
                }
                else {
                    postObj.name = this.personName;
                    postObj.mail = this.personMail;
                    postObj.abteilung = this.personAbteilung;
                }
                this.chosenItemKategorie = '';
                this.frageText = '';
                this.antwortText='';
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
