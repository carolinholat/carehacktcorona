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

        <v-btn @click="saveFAQ()">SPEICHERN</v-btn><br>
        <v-btn class="spaced" @click="showList = ! showList">Beiträge anzeigen</v-btn>
        <ItemDatatable
                v-if="showList"
                :headers="headers"
                :itemArray="beitraege"
                @showUpdateItem="showUpdateBeitrag($event)"
        />
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
    import ItemDatatable from '../ItemDatatable';



    export default {
        name: "FAQ",
        props: {
            abteilungenListe: Array,
            kategorienListe: Array,
            themenListe: Array,
            beitraege: Array,
            frageVonAbteilung: Array,
            frageVonKategorie: Array,
            frageVonThema: Array
        },
        components: {
            WarningOverlay,
            WarningDelete,
            ItemDatatable
        },
        data() {
            return {
                beitragId: 0,
                showList: false,
                frageText: '',
                antwortText: '',
                frageAbteilungen: [],
                frageKategorien: [],
                frageThemen: [],
                frageOeffentlich: false,
                abteilungFreigeben: null,
                kategorieFreigeben: null,
                overlay: false,
                headers:  [{text: 'Frage', value: 'frage'}, {text: 'Bearbeiten', value: 'link'}],
                ampel: ''
            }
        },
        methods: {
            showUpdateBeitrag(id){
                this.beitragId = id;
                let chosenBeitrag = this.beitraege.filter((user) => user.ID === id)[0];
                this.frageText = chosenBeitrag.frage;
                this.antwortText = chosenBeitrag.antwort;
                if (chosenBeitrag.freigegeben_fuer_gast === "1") {
                    this.frageOeffentlich = true;
                }
                else {
                    this.frageOeffentlich = false;
                }
                this.abteilungFreigeben = chosenBeitrag.freigegeben_fuer_abteilung;
                this.kategorieFreigeben = chosenBeitrag.freigegeben_fuer_kategorie;
                if (chosenBeitrag.wichtigkeit === 'normal') {
                    this.ampel = 'gruen';
                }
                else if (chosenBeitrag.wichtigkeit === 'wichtig') {
                    this.ampel = 'gelb';
                }
                else if (chosenBeitrag.wichtigkeit === 'sehr wichtig') {
                    this.ampel = 'rot';
                }

                // zugeordnete abteilungen, themen, kategorien filtern
                // ABTEILUNG
                let abteilungenMapArray = this.frageVonAbteilung.filter((item) => item.frage_ID === chosenBeitrag.ID);
                let abteilungen = [];
                for (let map of abteilungenMapArray) {
                    abteilungen.push(parseInt(map.abteilung_id));
                }
                for (let abteilung of this.abteilungenListe) {
                    if (abteilungen.includes(abteilung.value)) {
                        this.frageAbteilungen.push(abteilung);
                    }
                }

                // KATEGORIE
                let kategorienMapArray = this.frageVonKategorie.filter((item) => item.frage_ID === chosenBeitrag.ID);
                let kategorien = [];
                for (let map of kategorienMapArray) {
                    kategorien.push(parseInt(map.kategorie_id));
                }
                for (let kategorie of this.kategorienListe) {
                    if (kategorien.includes(kategorie.value)) {
                        this.frageKategorien.push(kategorie);
                    }
                }

                // Thema
                let themenMapArray = this.frageVonThema.filter((item) => item.frage_ID === chosenBeitrag.ID);
                let themen = [];
                for (let map of themenMapArray) {
                    themen.push(parseInt(map.thema_id));
                }
                for (let thema of this.themenListe) {
                    if (themen.includes(thema.value)) {
                        this.frageKategorien.push(thema);
                    }
                }
            },

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
                    let url = this.$store.state.url;
                    axios
                        .post(url + '/api/new_items.php', postObj)
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