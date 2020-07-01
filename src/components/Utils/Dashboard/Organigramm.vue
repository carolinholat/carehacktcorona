<template>
    <div>
        <v-radio-group v-model="select">
            <v-radio
                    :label="'Abteilungen'"
                    :value="'Abteilung'"
                    @change="reset()"
            ></v-radio>
            <v-radio
                    :label="'Bereiche'"
                    :value="'Kategorie'"
                    @change="reset()"
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
                <v-btn @click="save()">SPEICHERN</v-btn>
                <v-btn v-if="updateId !== 0" @click="reset()">ZURÜCK</v-btn>
            </div>
        </div>
        <ItemDatatable
                :headers="headers"
                :itemArray="select === 'Abteilung' ? abteilungenListe : kategorienListe"
                @showUpdateItem="showUpdateItem($event)"
                @deleteItem="tryDeleteItem($event)"
        />

        <WarningOverlay
                v-if="overlay"
                @weiter="overlay = false"
                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>
        <WarningDelete
                v-if="overlayDelete"
                @zurueck="overlayDelete = false"
                @loeschen="deleteItem(updateId)"/>
    </div>
</template>

<script>
    import WarningOverlay from "../../layout/WarningOverlay"
    import axios from "../../../plugins/axios";
    import ItemDatatable from '../ItemDatatable';
    import WarningDelete from "../../layout/WarningDelete"

    export default {
        name: "Organigramm",
        props: {
            abteilungenListe: Array,
            kategorienListe: Array,
            themenListe: Array,
            kategorieHatAbteilung: Array
        },
        components: {
            WarningOverlay,
            ItemDatatable,
            WarningDelete
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
                select: 'Abteilung',
                /* search: '', */
                headers: [{text: 'Bezeichnung', value: 'text'}, {text: 'Bearbeiten', value: 'link'}, {
                    text: 'Löschen',
                    value: 'delete'
                }],
                filteredItems: [],
                overlay: false,
                overlayDelete: false
            }
        },
        methods: {
            handleErrors(error) {
                if(error /*.response.status < 200 || error.response.status > 299 */) {
                    this.$store.commit('setServerBug', true);
                }
            },
            reset() {
                this.updateId = 0;
                this.newItem = '';
                this.newItemChildren = [];
                this.abteilung = '';
                this.abteilungId = 0;
                this.kategorieId = 0;
                this.filteredItems = [];
            },
            showUpdateItem(id) {
                this.updateId = id;
                this.addNew = true;
                let list = this.select === 'Abteilung' ? this.abteilungenListe : this.kategorienListe;
                let item = list.filter((item) => item.value === id)[0];
                this.newItem = item.text;

                // zugeordnete abteilungen/kategorien filtern
                let zugewiesenMapArray = [];
                let zugewiesen = [];

                if(this.select === 'Abteilung') {
                    zugewiesenMapArray = this.kategorieHatAbteilung.filter((obj) => parseInt(obj.abteilung_id) === item.value);
                    for(let map of zugewiesenMapArray) {
                        zugewiesen.push(parseInt(map.kategorie_id));
                    }
                } else if(this.select === 'Kategorie') {
                    zugewiesenMapArray = this.kategorieHatAbteilung.filter((obj) => parseInt(obj.kategorie_id) === item.value);
                    for(let map of zugewiesenMapArray) {
                        zugewiesen.push(parseInt(map.abteilung_id));
                    }
                }

                for(let abteilungBzwKategorie of list) {
                    if(zugewiesen.includes(abteilungBzwKategorie.value)) {
                        this.newItemChildren.push(abteilungBzwKategorie);
                    }
                }
            },
            getFiltered(e) {
                this.filteredItems = e;
            },
            deleteItem(id) {
                let postObj = {};
                postObj.ID = id;
                if(this.select === 'Abteilung') {
                    postObj.action = 'abteilungDelete';
                } else if(this.select === 'Kategorie') {
                    postObj.action = 'kategorieDelete';
                }
                let self = this;
                let url = this.$store.state.url;
                axios
                    .post(url + '/api/new_items.php', postObj)
                    .then()
                    .catch(error => self.handleErrors(error));
                this.$emit('loadNew');
                this.overlayDelete = false;
            },
            tryDeleteItem(id) {
                this.updateId = id;
                this.overlayDelete = true;
            },
            save() {
                if(this.newItem === '' || this.newItemChildren.length < 1) {
                    this.overlay = true;
                } else {
                    let postObj = {};
                    if(this.updateId === 0) {
                        if(this.select === 'Abteilung') {
                            postObj.action = 'abteilungInsert';
                            postObj.abteilung = this.newItem;
                            postObj.kategorien = this.newItemChildren;
                        } else if(this.select === 'Kategorie') {
                            postObj.action = 'kategorieInsert';
                            postObj.kategorie = this.newItem;
                            postObj.abteilungen = this.newItemChildren;
                        }
                    } else {
                        postObj.ID = this.updateId;
                        if(this.select === 'Abteilung') {
                            postObj.action = 'abteilungUpdate';
                            postObj.abteilung = this.newItem;
                            postObj.kategorien = this.newItemChildren;
                        } else if(this.select === 'Kategorie') {
                            postObj.action = 'kategorieUpdate';
                            postObj.kategorie = this.newItem;
                            postObj.abteilungen = this.newItemChildren;
                        }
                    }
                    let self = this;
                    let url = this.$store.state.url;

                    axios
                        .post(url + '/api/new_items.php', postObj)
                        .then(self.$emit('loadNew'))
                        .catch(error => self.handleErrors(error));
                }
                this.reset();
               // this.$emit('loadNew');
            }
        }
    }
</script>

<style scoped>

</style>