<template>
    <div>
        <v-radio-group v-model="select">
            <v-radio
                    :label="'Abteilungen'"
                    :value="'Abteilung'"
            ></v-radio>
            <v-radio
                    :label="'Bereiche'"
                    :value="'Kategorie'"
            ></v-radio>
        </v-radio-group>

        <div v-if="select !== ''">
            <v-btn @click="addNew = ! addNew">{{select.toUpperCase()}} NEU ANLEGEN / BEARBEITEN</v-btn>
            <div v-if="addNew">

                <v-text-field
                        v-model="newItem"
                        placeholder="Neue Organisationseinheit">
                </v-text-field>

                <v-select
                        class="spaced"
                        label="Zuweisen"
                        multiple
                        :items="select === 'Kategorie' ? abteilungenListe : kategorienListe"
                        v-model="newItemChildren">
                </v-select>
                <v-btn>SPEICHERN</v-btn>
            </div>
        </div>

        <v-text-field
                v-if="select !== ''"
                v-model="search"
                append-icon="mdi-magnify"
                label="Suche"
                single-line
                hide-details
        ></v-text-field>
        <v-data-table v-if="select !== ''"
                      :headers="headers"
                      :items="select === 'Abteilung' ? abteilungenListe : kategorienListe"
                      :items-per-page="5"
                      :search="search"
                      @current-items="getFiltered"
                      class="elevation-1">
            <template v-slot:body="{ items }">
                <tr v-for="item in items" :key="item.value">
                    <td v-for="(header, index) of headers" :key="header.value + index">
                        <template v-if="header.value !== 'link'">
                            {{item[header.value]}}
                        </template>
                        <template v-else-if="header.value === 'link'">
                            <v-btn @click="showUpdateItem(item.value)">
                                BEARBEITEN
                            </v-btn>
                        </template>
                        <template v-else-if="header.value === 'delete'">
                            <v-btn @click="deleteItem(item.value)">
                                BEARBEITEN
                            </v-btn>
                        </template>
                    </td>
                </tr>
            </template>
        </v-data-table>
        <WarningOverlay
                v-if="overlay"
                @weiter="overlay = false"
                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>
    </div>
</template>

<script>
    import WarningOverlay from "../../layout/WarningOverlay"
    import axios from "../../../plugins/axios";

    export default {
        name: "Organigramm",
        props: {
            abteilungenListe: Array,
            kategorienListe: Array,
            themenListe: Array

        },
        components: {
            WarningOverlay
        },
        data() {
            return {
                addNew: false,
                updateId: 0,
                newItem: '',
                newItemChildren: [],
                abteilung: '',
                abteilungId: 0,
                kategorieId: 0,
                select: '',
                search: '',
                headers: [{text: 'Bezeichnung', value: 'text'}, {text: 'Bearbeiten', value: 'link'}],
                filteredItems: [],
                overlay: false
            }
        },
        methods: {
            showUpdateItem(id) {
                this.updateId = id;
                this.addNew = true;
                let list = this.select === 'Abteilung' ? this.abteilungenListe : this.kategorienListe;
                let item = list.filter((item) => item.value === id)[0];
                this.newItem = item.text;
            },
            getFiltered(e) {
                this.filteredItems = e;
            },
            deleteItem(id) {

            }
        }
    }
</script>

<style scoped>

</style>