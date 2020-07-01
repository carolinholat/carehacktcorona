<template>
    <v-overlay
            absolute="absolute"
            :z-index="2000"
    >
        <v-card grid-list-md text-xs-center style="min-width: 200px; margin: auto; margin-top: 150px; padding: 10px">

            <v-card-text align="center" style="width: 80%; margin: auto; padding: 30px">
                Prüfen Sie erstmal unter "Suche", ob die Frage bereits beantwortet wurde!
            </v-card-text>

            <v-divider></v-divider>
            <v-text-field
                    label="Ihre Frage"
                    placeholder="und zwar..."
                    v-model="questionText"
            ></v-text-field>

            <v-card-actions class="justify-center">
                <v-spacer></v-spacer>
                <v-btn
                        class="success justify-center"
                        text
                        @click="saveFrage()"
                >
                    Absenden
                </v-btn>
                <v-btn
                        class="warning justify-center"
                        text
                        @click="$emit('zurueck')"
                >
                    ZURÜCK
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-overlay>
</template>

<script>
    import axios from "../../plugins/axios";
    export default {
        name: "InputFrage",
        data() {
            return {
                questionText: ''
            }
        },
        methods: {
            handleErrors(error) {
                if(error /*.response.status < 200 || error.response.status > 299 */) {
                    this.$store.commit('setServerBug', true);
                }
            },
            saveFrage() {
                let postObj = {};
                postObj.action = 'frageInsertUnbeantwortet';
                postObj.frage = this.questionText;
                let self = this;
                let url = this.$store.state.url;
                axios
                    .post(url + '/api/new_items.php', postObj)
                    .then(self.$emit('zurueck'))
                    .catch(error => self.handleErrors(error));
            },
            zurueck() {

            }
        }
    }
</script>

<style scoped>

</style>