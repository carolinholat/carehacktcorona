<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <h2 align="center" class="spaced">WIKI</h2>
            <v-row>
                <v-col cols="10" class="mx-auto">
                    <v-select
                            class="spaced"
                            align="center"
                            :items="itemKategorie"
                            v-model="chosenItemKategorie"
                            label="Infos nach Abteilungen gefiltert"
                    ></v-select>

                </v-col>
            </v-row>
        </v-card>

    </div>
</template>

<script>
    import axios from "../plugins/axios";

    export default {
        components: {},
        methods: {
        },
        data() {
            return {
                itemKategorie: ['Frage & Antwort', 'Abteilung', 'Person', 'Themenfeld'],
                chosenItemKategorie: ''
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
