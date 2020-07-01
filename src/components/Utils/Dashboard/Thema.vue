<template>
    <div>
        <v-text-field
                label="Themenfeld"
                placeholder="Themenfeldbezeichnung"
                v-model="thema"
        ></v-text-field>
        <v-select
                v-if="abteilungenListe.length > 0"
                class="spaced"
                label="Thema einer oder mehreren Abteilungen zuweisen"
                multiple=""
                :items="abteilungenListe"
                v-model="themaAbteilungen">
        </v-select>
        <v-btn @click="saveThema()">SPEICHERN</v-btn>
        <v-btn v-if="updateThema !== 0" @click="reset()">ZURÜCK</v-btn>
        <br>

        <v-btn class="spaced"
               v-if="themenListe.length > 0"
               @click="showList = ! showList">Themenverzeichnis anzeigen</v-btn>

        <ItemDatatable
                v-if="showList"
                :headers="headers"
                :itemArray="themenListe"
                @showUpdateItem="showUpdateThema($event)"
                @deleteItem="tryDeleteThema($event)"
        />
        <WarningOverlay
                v-if="overlay"
                @weiter="overlay = false"
                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>
        <WarningDelete
                v-if="overlayDelete"
                @zurueck="overlayDelete = false"
                @loeschen="deleteThema(updateThema)"/>
    </div>
</template>

<script>
    import axios from "../../../plugins/axios";
    import WarningOverlay from "../../layout/WarningOverlay"
    import WarningDelete from "../../layout/WarningDelete"
    import ItemDatatable from '../ItemDatatable';


    export default {
        name: "Thema",
        props: {
            abteilungenListe: Array,
            themenListe: Array,
            themaVonAbteilung: Array
        },
        components: {
            WarningOverlay,
            ItemDatatable,
            WarningDelete
        },
        data() {
            return {
                updateThema: 0,
                thema: '',
                themaAbteilungen: [],
                overlay: false,
                showList: false,
                headers: [{text: 'Text', value: 'text'},  {text: 'Bearbeiten', value: 'link'}, {text: 'Löschen', value: 'delete'}],
                overlayDelete: false
            }
        },
        methods: {
            reset() {
               this.updateThema = 0;
               this.thema = '';
               this.themaAbteilungen = [];
            },
            saveThema() {
                let postObj = {};
                if (this.updateThema === 0) {
                    postObj.action = 'themaInsert';
                }
                else {
                    postObj.action = 'themaUpdate';
                    postObj.ID = this.updateThema;
                }
                postObj.thema = this.thema;
                postObj.abteilungen = this.themaAbteilungen;
                if (this.thema === '' || this.themaAbteilungen.length < 1) {
                    this.overlay = true;
                }
                else {
                    let url = this.$store.state.url;
                    axios
                        .post(url + '/api/new_items.php', postObj)
                        .then(response => console.log(response.data));
                    this.thema= '';
                    this.themaAbteilungen = [];
                    this.$emit('loadNew');
                }
            },
            showUpdateThema(id) {
                this.updateThema = id;
                let chosenThema = this.themenListe.filter((user) => user.value === id)[0];
                this.thema = chosenThema.text;
                // zugeordnete abteilungen filtern
                let abteilungenMapArray = this.themaVonAbteilung.filter((item) => parseInt(item.thema_ID) === chosenThema.value);

                let abteilungen = [];
                for (let map of abteilungenMapArray) {
                    abteilungen.push(parseInt(map.abteilung_id));
                }
                for (let abteilung of this.abteilungenListe) {
                    if (abteilungen.includes(abteilung.value)) {
                        this.themaAbteilungen.push(abteilung);
                    }
                }
            },
            deleteThema(id) {
                this.updateThema = id;
                this.$emit('loadNew');
                this.overlayDelete = false;
                let postObj = {};
                postObj.action = 'themaDelete';
                postObj.ID = id;
                let url = this.$store.state.url;
                axios
                    .post(url + '/api/new_items.php', postObj)
                    .then(response => console.log(response.data));
            },
            tryDeleteThema(id) {
                this.updateThema = id;
                this.overlayDelete = true;
            }
        }
    }
</script>

<style scoped>

</style>