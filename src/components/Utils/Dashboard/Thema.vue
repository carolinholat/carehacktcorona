<template>
    <div>
        <v-text-field
                label="Themenfeld"
                placeholder="Themenfeldbezeichnung"
                v-model="thema"
        ></v-text-field>
        <v-select
                class="spaced"
                label="Thema einer oder mehreren Abteilungen zuweisen"
                multiple=""
                :items="abteilungenListe"
                v-model="themaAbteilungen">
        </v-select>
        <v-btn @click="saveThema()">SPEICHERN</v-btn>
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
        name: "Thema",
        props: {
            abteilungenListe: Array,
            themenListe: Array
        },
        components: {
            WarningOverlay
        },
        data() {
            return {
                thema: '',
                themaAbteilungen: [],
                overlay: false
            }
        },
        methods: {
            saveThema() {
                let postObj = {};
                postObj.action = 'themaInsert';
                postObj.thema = this.thema;
                postObj.abteilungen = this.themaAbteilungen;
                if (this.thema === '' || this.themaAbteilungen.length < 1) {
                    this.overlay = true;
                }
                else {
                    axios
                        .post('http://localhost:8000/api/new_items.php', postObj)
                        .then(response => console.log(response.data));
                    this.thema= '';
                    this.themaAbteilungen = [];
                }
            }
        }
    }
</script>

<style scoped>

</style>