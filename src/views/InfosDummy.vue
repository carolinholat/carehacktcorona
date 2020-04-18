<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <h2 align="center" class="spaced">WIKI</h2>
            <v-row>
                <v-col cols="10" class="mx-auto">
                    <v-select
                            class="spaced"
                            align="center"
                            :items="abteilungen"
                            v-model="chosenAbteilung"
                            label="Infos nach Abteilungen filtern"
                    ></v-select>
                    <v-select
                            class="spaced"
                            align="center"
                            :items="themen"
                            v-model="chosenThema"
                            label="Infos nach Themen filtern"
                    ></v-select>
                    <v-spacer style="height: 30px"></v-spacer>
                    <v-autocomplete
                            :items="['abc', 'def']"
                            :search-input.sync="filter"
                            color="white"
                            hide-no-data
                            hide-selected
                            label="Fragetext"
                            placeholder="Nach Fragetext suchen"
                            prepend-icon="mdi-database-search"
                            return-object
                    ></v-autocomplete>
                    <div v-for="frage in filteredFragen" :key="frage.ID">
                        <h4>{{frage.frage}}</h4>
                        <p>{{frage.timestamp}}</p>
                        <p>{{frage.antwort}}</p>
                    </div>

                </v-col>
            </v-row>
        </v-card>

    </div>
</template>

<script>
    import axios from "../plugins/axios";

    export default {
        components: {},
        mounted() {
            let self = this;
        axios.post('http://localhost:8000/carehacktcorona/api/init.php', 'themenabteilungenfragen')
                .then(response => self.initThemenUndAbteilungen(response.data));
        },
        methods: {
            initThemenUndAbteilungen(data) {
                let themen = data.themen;
                let themenPlaceholder = [];
                for (let id of Object.keys(themen)) {
                    let thema = {};
                    thema.value = id;
                    thema.text = themen[id].name;
                    themenPlaceholder.push(thema);
                }
                this.themen = themenPlaceholder;
                let abteilungen = data.abteilungen;
                let abteilungPlaceholder = [];
                for (let id of Object.keys(abteilungen)) {
                    let abteilung = {};
                    abteilung.value = id;
                    abteilung.text = abteilungen[id].name;
                    abteilungPlaceholder.push(abteilung);
                }
                this.abteilungen = abteilungPlaceholder;

                let fragen = data.fragen;
                this.filteredFragen = fragen;
            }
        },
        data() {
            return {
                abteilungen: [{text: 'Abteilung 1', value: 1}, {text: 'Abteilung 2', value: 2}],
                chosenAbteilung: null,
                themen: ['Thema 1', 'Thema 2'],
                chosenThema: null,
                items: ['fragetext1', 'fragetext2'],
                chosenItem: null,
                filteredFragen: [{ID: 1, timestamp: 123, frage: 'Eine Beispielfrage', antwort: 'Eine Beispielantwort'}],
                filter: null
            }
        }
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
