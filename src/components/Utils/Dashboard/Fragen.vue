<template>
    <div>
        <div v-if="frage !== null">
            <h4>{{frage.frage}}</h4>
            <p class=".font-italic font-weight-light">Frage am: {{frage.zeitpunkt_erstellung}}</p>
            <v-card class="cardContent">
                <v-text-field placeholder="Ihr Antworttext" v-model="inputAntwort"></v-text-field>
            </v-card>
            <v-select
                    class="spaced"
                    label="Frage nur für eine Abteilung freigeben?"
                    :items="abteilungenListe"
                    v-model="abteilungFreigeben">
            </v-select>
            <v-select
                    class="spaced"
                    label="Frage nur für einen Bereich freigeben?"
                    :items="kategorienListe"
                    v-model="kategorieFreigeben">
            </v-select>
            <v-select
                    class="spaced"
                    label="Frage einer oder mehreren Abteilungen zuweisen"
                    multiple
                    :items="abteilungenListe"
                    v-model="frageAbteilungen">
            </v-select>
            <v-select
                    class="spaced"
                    label="Frage einer oder mehreren Bereichen zuweisen"
                    multiple
                    :items="kategorienListe"
                    v-model="frageKategorien">
            </v-select>
            <v-select
                    class="spaced"
                    label="Frage einer oder mehreren Themenen zuweisen"
                    multiple
                    :items="themenListe"
                    v-model="frageThemen">
            </v-select>
            <v-switch class="spaced" v-model="frageOeffentlich" label="Öffentlich sichtbar?"></v-switch>

            <v-btn class="spaced" @click="saveFrage()">SPEICHERN</v-btn>
            <v-btn class="spaced" @click="frage = null">ZURÜCK ZU ÜBERSICHT</v-btn>
        </div>
        <v-data-table
                v-if="fragen.length > 0"
                style="margin-top: 30px"
                :headers="headers"
                :items="fragen"
                :items-per-page="5"
                class="elevation-1">
            <template v-slot:body="{ items }">
                <tr v-for="item in items" :key="item.ID">
                    <td v-for="(header, index) of headers" :key="header.value + index">
                        <template v-if="header.value !== 'link'">
                            {{item[header.value]}}
                        </template>
                        <template v-else-if="header.value === 'link'">
                            <v-btn @click="frage = item">
                                BEANTWORTEN
                            </v-btn>
                        </template>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <h2 v-else>Keine Fragen offen</h2>
        <WarningOverlay
                v-if="overlay"
                @weiter="overlay = false"
                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>

    </div>
</template>

<script>
    import axios from "../../../plugins/axios";
    import WarningOverlay from "../../layout/WarningOverlay"


    export default {
        name: "Fragen",
        props: {
            fragen: Array,
            kategorienListe: Array,
            abteilungenListe: Array,
            themenListe: Array
        },
        components: {
            WarningOverlay
        },
        data() {
            return {
                frage: null,
                frageAbteilungen: [],
                abteilungFreigeben: null,
                kategorieFreigeben: null,
                frageKategorien: [],
                frageThemen: [],
                frageOeffentlich: false,
                inputAntwort: '',
                ID: 0,
                headers: [{text: 'Frage', value: 'frage'}, {
                    text: 'Zeitpunkt Erstellung',
                    value: 'zeitpunkt_erstellung'
                }, {text: 'Beantworten', value: 'link'}],
                overlay: false
            }
        },
       methods: {
           handleErrors(error) {
               if (error /*.response.status < 200 || error.response.status > 299 */ ) {
                   this.$store.commit('setServerBug', true);
               }
           },
            saveFrage() {
                let postObj = {};
                postObj.action = 'frageUpdate';
                postObj.ID = parseInt(this.frage.ID);
                postObj.antwort = this.inputAntwort;
                postObj.abteilungen = this.frageAbteilungen;
                postObj.kategorien = this.frageKategorien;
                postObj.themen = this.frageThemen;
                postObj.freigeben_abteilung = this.abteilungFreigeben;
                postObj.freigeben_kategorie = this.kategorieFreigeben;
                let oeffentlich = 0;
                if (this.frageOeffentlich) {
                    oeffentlich = 1;
                }
                postObj.oeffentlich = oeffentlich;
                let self = this;
                if (this.inputAntwort === '' || (this.frageAbteilungen.length < 1 && this.frageKategorien.length < 1) ) {
                   this.overlay = true;
                }
                else {
                    let url = this.$store.state.url;
                    axios
                        .post(url + '/api/new_items.php', postObj)
                        .then(response => self.reload())
                        .catch(error => self.handleErrors(error));
                }
            },
            reload() {
                this.frage= null;
                this.inputAntwort = '';
                this.frageOeffentlich = false;
                this.abteilungFreigeben = null;
                this.kategorieFreigeben = null;
                this.$emit('loadFragen');
            }
       }
    }
</script>

<style scoped>

</style>