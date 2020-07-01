<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <v-row>
                <v-col cols="10" class="mx-auto">
                    <div>
                        <h2 align="center" class="spaced">Feedback</h2>
                        <v-card>
                            <p style="padding: 20px">
                                <v-text-field
                                        label="Das hätte ich gerne oder das stört mich:"
                                        placeholder="Und zwar:"
                                        v-model="antwortText"
                                ></v-text-field>
                            </p>
                        </v-card>
                        <v-btn @click="save()">SENDEN</v-btn>
                        <WarningOverlay
                                v-if="overlay"
                                @weiter="overlay = false"
                                :msg="'Es fehlen noch Angaben. Klicken Sie, um dieses Fenster zu schließen'"/>
                    </div>
                </v-col>
            </v-row>
        </v-card>
        <WarningServerBug v-if="$store.state.serverBug"/>
    </div>
</template>

<script>
    import axios from "../plugins/axios";
    import WarningOverlay from "../../src/components/layout/WarningOverlay"
    import WarningServerBug from '../../src/components/layout/WarningServerBug'

    export default {
        components: {
            WarningOverlay,
            WarningServerBug
        },
        data() {
            return {
                antwortText: '',
                overlay: false
            }
        },
        mounted() {
        },
        methods: {
            handleErrors(error) {
                if (error /*.response.status < 200 || error.response.status > 299 */ ) {
                    this.$store.commit('setServerBug', true);
                }
            },
            save() {
                let postObj = {};
                postObj.action = 'feedbackInsert';
                postObj.text = this.antwortText;

                if (this.antwortText === '') {
                    this.overlay = true;
                }
                else {
                    let self = this;
                    let url = this.$store.state.url;
                    axios
                        .post(url + '/api/new_items.php', postObj)
                        .then(response => self.antwortText = '')
                        .catch(error => self.handleErrors(error));
                }
            }
        },
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

    .routerLink {
        text-decoration: none;
        color: whitesmoke;
    }

    .routerLink:hover {
        text-decoration: none;
        color: yellow;
    }

</style>
