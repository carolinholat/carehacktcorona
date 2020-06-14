<template>
    <div>
        <v-btn class="spaced" @click="addNew = !addNew">{{ updateUser === 0 ? 'MITARBEITER*IN HINZUFÜGEN' : 'MITARBEITER*IN BEARBEITEN'}}</v-btn>
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

            <v-btn class="spaced" @click="saveUser()">SPEICHERN</v-btn>
            <v-btn v-if="updateUser !== 0" @click="reset()">ZURÜCK</v-btn>
        </div>
        <v-btn class="spaced" @click="showList = ! showList">Mitarbeiter*innenverzeichnis anzeigen</v-btn>
        <ItemDatatable
                v-if="showList"
                :headers="headers"
                :itemArray="userArray"
                @showUpdateItem="showUpdateUser($event)"
                @deleteItem="tryDeleteUser($event)"
        />

        <WarningOverlay
                v-if="overlay"
                @weiter="overlay = false"
                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>
        <WarningDelete
                v-if="overlayDelete"
                @zurueck="overlayDelete = false"
                @loeschen="deleteUser(updateUser)"/>
    </div>
</template>

<script>
    import axios from "../../../plugins/axios";
    import WarningOverlay from "../../layout/WarningOverlay"
    import WarningDelete from "../../layout/WarningDelete"
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
            ItemDatatable,
            WarningDelete
        },
        methods: {
            showUpdateUser(id) {

                this.updateUser = id;
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
                    let url = this.$store.state.url;
                    axios
                        .post(url + '/api/new_items.php', postObj)
                        .then(response => console.log(response.data));
                    this.personVorname = '';
                    this.personName = '';
                    this.personMail = '';
                    this.personAbteilung = '';
                    this.addNew = false;
                    this.$emit('loadUser');
                }
            },

            deleteUser(id = false) {
                let postObj = {};
                postObj.action = 'userDelete';
                if (id) {
                    postObj.ID = id;
                }
                else {
                    postObj.ID = this.updateUser;
                }
                let url = this.$store.state.url;
                axios
                    .post(url + '/api/new_items.php', postObj)
                    .then(response => console.log(response.data));
                this.personVorname = '';
                this.personName = '';
                this.personMail = '';
                this.personAbteilung = '';
                this.addNew = false;
                this.$emit('loadUser');
                this.overlayDelete = false;
            },
            tryDeleteUser(id) {
                this.updateUser = id;
                this.overlayDelete = true;
            },
            reset() {
                this.updateUser = 0;
                this.personVorname = '';
                this.personName = '';
                this.personMail = '';
                this.personAbteilung = '';
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
                }, {text: 'Bearbeiten', value: 'link'}, {text: 'Löschen', value: 'delete'}],
                overlay: false,
                overlayDelete: false
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