<template>
    <div>
        <v-btn class="spaced" @click="addNew = !addNew">{{hinzuOderBearbeitenText}}</v-btn>
        <br/>
        <div v-if="addNew">
            <v-text-field
                    label="Person"
                    placeholder="Vorname Person"
                    v-model="personVorname"
            ></v-text-field>
            <v-text-field
                    label="Person"
                    placeholder="Name Person"
                    v-model="personName"
            ></v-text-field>
            <v-text-field
                    label="Mail"
                    placeholder="Mailadresse Person"
                    v-model="personMail"
            ></v-text-field>
            <v-select
                    label="Abteilung der Person"
                    :items="abteilungenListe"
                    v-model="personAbteilung"
            ></v-select>

            <v-btn class="spaced" v-if="updateUser > 0" @click="deleteUser()">MITARBEITER*IN LÖSCHEN</v-btn>
            <br/>
            <v-btn class="spaced" @click="saveUser()">SPEICHERN</v-btn>
        </div>
        <v-btn class="spaced" @click="showList = ! showList">Mitarbeiter*innenverzeichnis anzeigen</v-btn>
        <ItemDatatable
                v-if="showList"
                :headers="headers"
                :itemArray="userArray"
                @showUpdateItem="showUpdateUser($event)"
        />
        <!--<v-text-field
                v-if="showList"
                v-model="search"
                append-icon="mdi-magnify"
                label="Suche"
                single-line
                hide-details
        ></v-text-field>
        <v-data-table v-if="showList"
                      :headers="headers"
                      :items="userArray"
                      :items-per-page="5"
                      :search="search"
                      class="elevation-1">
            <template v-slot:body="{ items }">
                <tr v-for="item in items" :key="item.ID">
                    <td v-for="(header, index) of headers" :key="header.value + index">
                        <template v-if="header.value !== 'link'">
                            {{item[header.value]}}
                        </template>
                        <template v-else-if="header.value === 'link'">
                            <v-btn @click="showUpdateUser(item.ID)">
                                BEARBEITEN
                            </v-btn>
                        </template>
                    </td>
                </tr>
            </template>
        </v-data-table> -->

        <WarningOverlay
                v-if="overlay"
                @weiter="overlay = false"
                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>
    </div>
</template>

<script>
    import axios from "../../../plugins/axios";
    import WarningOverlay from "../../layout/WarningOverlay"
    import ItemDatatable from '../ItemDatatable';

    export default {
        name: "Mitarbeiter",
        props: {
            user: Array,
            abteilungen: Object,
            kategorien: Object,
            abteilungenListe: Array
        },
        components: {
            WarningOverlay,
            ItemDatatable
        },
        methods: {
            showUpdateUser(id) {

                this.updateUser = id;
                this.hinzuOderBearbeitenText = 'Mitarbeiterdaten bearbeiten'
                let chosenUser = this.user.filter((user) => user.ID === id)[0];
                this.personName = chosenUser.name;
                this.personVorname = chosenUser.vorname;
                this.personMail = chosenUser.mail;
                this.personAbteilung = parseInt(chosenUser.abteilung_id);
                this.addNew = true;
            },

            saveUser() {
                let postObj = {};
                postObj.action = this.updateUser > 0 ? 'userUpdate' : 'userInsert';
                if(postObj.action === 'userUpdate') {
                    postObj.ID = this.updateUser;
                }
                postObj.vorname = this.personVorname;
                postObj.name = this.personName;
                postObj.mail = this.personMail;
                postObj.abteilung = this.personAbteilung;
                if (this.personVorname === '' || this.personName === '' || this.personMail === '' || this.personAbteilung === '') {
                    this.overlay = true;
                }
                else {
                    axios
                        .post('http://localhost:8000/api/new_items.php', postObj)
                        .then(response => console.log(response.data));
                    this.personVorname = '';
                    this.personName = '';
                    this.personMail = '';
                    this.personAbteilung = '';
                    this.addNew = false;
                    this.$emit('loadUser');
                }
            },

            deleteUser() {
                let postObj = {};
                postObj.action = 'userDelete';
                postObj.ID = this.updateUser;
                axios
                    .post('http://localhost:8000/api/new_items.php', postObj)
                    .then(response => console.log(response.data));
                this.personVorname = '';
                this.personName = '';
                this.personMail = '';
                this.personAbteilung = '';
                this.addNew = false;
                this.$emit('loadUser');
            }
        },
        data() {
            return {
                search: '',
                hinzuOderBearbeitenText: 'Neue Mitarbeiter*innen hinzufügen',
                updateUser: 0,
                addNew: false,
                showList: false,
                personName: '',
                personVorname: '',
                personMail: '',
                personAbteilung: '',
                headers: [{text: 'Vorname', value: 'vorname'}, {text: 'Name', value: 'name'}, {
                    text: 'Abteilung',
                    value: 'abteilung'
                }, {text: 'Bearbeiten', value: 'link'}],
                overlay: false
            }
        },
        computed: {
            userArray: function() {
                let userArray = [];
                for(let user of this.user) {
                    let abteilung = parseInt(user.abteilung_id);
                    let userAbteilung = this.abteilungen[abteilung].name;
                    user.abteilung = userAbteilung;
                    userArray.push(user);
                }
                return userArray;
            },
        }
    }
</script>

<style scoped>

</style>