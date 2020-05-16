<template>
    <div>
        <v-text-field
                label="Frage"
                placeholder="Ihr Fragetext"
                v-model="frageText"
        ></v-text-field>
        <v-text-field
                label="Antwort"
                placeholder="Ihr Antworttext"
                v-model="antwortText"
        ></v-text-field>
        <div class="d-inline-flex">
            <v-checkbox
                    v-model="ampel"
                    label="Als Push-Notification senden"
                    color="red"
                    value="rot"
                    hide-details
            ></v-checkbox>
            <v-checkbox
                    v-model="ampel"
                    label="Als Email senden"
                    color="yellow"
                    value="gelb"
                    hide-details
            ></v-checkbox>
            <v-checkbox
                    v-model="ampel"
                    label="Nur zuweisen"
                    color="green"
                    value="gruen"
                    hide-details
            ></v-checkbox>
        </div>
        <v-select
                class="spaced"
                label="Frage einer oder mehreren Abteilungen zuweisen"
                multiple=""
                :items="abteilungenListe"
                v-model="frageAbteilungen">
        </v-select>
        <v-select
                class="spaced"
                label="Frage einer oder mehreren Bereichen zuweisen"
                multiple=""
                :items="kategorienListe"
                v-model="frageKategorien">
        </v-select>
        <v-select
                class="spaced"
                label="Frage einer oder mehreren Themen zuweisen"
                multiple=""
                :items="themenListe"
                v-model="frageThemen">
        </v-select>
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
        <v-switch class="spaced" v-model="frageOeffentlich" label="Öffentlich sichtbar?"></v-switch>

        <v-btn @click="saveFAQ()">SPEICHERN</v-btn>
        <WarningOverlay
                v-if="overlay"
                @weiter="overlay = false"
                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>
       <!-- <WarningDelete/> -->

    </div>

</template>

<script>
    import axios from "../../../plugins/axios";
    import WarningOverlay from "../../layout/WarningOverlay"
    import WarningDelete from "../../layout/WarningDelete"


    export default {
        name: "FAQ",
        props: {
            abteilungenListe: Array,
            kategorienListe: Array,
            themenListe: Array
        },
        components: {
            WarningOverlay,
            WarningDelete
        },
        data() {
            return {
                frageText: '',
                antwortText: '',
                frageAbteilungen: [],
                frageKategorien: [],
                frageThemen: [],
                frageOeffentlich: false,
                abteilungFreigeben: null,
                kategorieFreigeben: null,
                overlay: false,
                ampel: ''
            }
        },
        methods: {
            saveFAQ() {
                let postObj = {};
                postObj.action = 'frageInsert';
                postObj.frage = this.frageText;
                postObj.antwort = this.antwortText;
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
                if (this.frageText === '' || this.antwortText === '' || (this.frageAbteilungen.length < 1 && this.frageKategorien.length < 1) ) {
                    this.overlay = true;
                }
                else {
                    axios
                        .post('http://localhost:8000/api/new_items.php', postObj)
                        .then(response => self.reload());
                }

            },
            reload() {
                this.$emit('loadFragen');
                this.frageText = '';
                this.antwortText = '';
                this.frageAbteilungen = [];
                this.frageKategorien = [];
                this.frageOeffentlich = false;
            }
        }
    }
</script>

<style scoped>

</style>