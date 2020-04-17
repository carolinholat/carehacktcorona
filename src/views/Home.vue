<template>
    <div>
        <v-card class="cardCentered align-center" style="margin-top: 50px">
            <h2 align="center" class="spaced">Kontaktformular Coronameldung</h2>
            <v-row>
                <v-col cols="10" class="mx-auto">
                    <v-select
                            class="spaced"
                            align="center"
                            :items="infoOderMeldung"
                            v-model="infoOderMeldungA"
                            label="Brauchen Sie Informationen oder wollen Sie einen Verdacht melden"
                            @change="setStep(1)"
                    ></v-select>
                    <v-select
                            v-if="step > 1"
                            class="spaced"
                            multiple
                            align="center"
                            :items="risikogebiete"
                            v-model="risikogebieteA"
                            label="Waren Sie in einem dieser Risikoländer?"
                            @change="setStep(2)"
                    ></v-select>
                    <v-select
                            v-if="step > 2"
                            class="spaced"
                            align="center"
                            :items="kontakt"
                            v-model="kontaktA"
                            label="Hatten Sie Kontakt zu einer Person, die positiv auf das Virus getestet wurde?"
                            @change="setStep(3)"
                    ></v-select>
                    <v-slider
                            v-if="step > 3 && kontaktA === 'nicht sicher'"
                            class="spaced"
                            v-model="wahrscheinlichkeit"
                            :min="0"
                            :max="100"
                            thumb-label
                            label="Wie hoch schätzen Sie die Wahrscheinlichkeit in % ein, dass die Person angesteckt ist?"
                            @change="setStep(4)"
                    ></v-slider>
                    <v-select
                            v-if="step > 4"
                            class="spaced"
                            multiple
                            align="center"
                            :items="symptome"
                            v-model="symptomeA"
                            label="Haben Sie eins oder mehrere folgender Symptome?"
                            @change="setStep(5)"
                    ></v-select>
                    <v-select
                            v-if="step > 5"
                            class="spaced"
                            multiple
                            align="center"
                            :items="fremdgefaehrdung"
                            v-model="fremdgefaehrdungA"
                            label="Haben Sie Kontakt mit gefährdeten Personengruppen?"
                            @change="setStep(6)"
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
            setStep(current) {
                switch(current) {
                    case 1:
                        if(this.infoOderMeldungA === 'Ich brauche Informationen') {
                            this.step = 1;
                            this.showInfoBox = true;
                        } else {
                            this.step = 2;
                            this.showInfoBox = false;
                        }
                        break;
                    case 2:
                        this.step = 3;
                        break;
                    case 3:
                        if (this.kontaktA === "nicht sicher") {
                           this.step = 4;
                        }
                        else this.step = 5;
                        break;
                    case 4:
                        this.step = 5;
                        break;
                    case 5:
                        this.step = 6;
                        break;
                    case 6:
                        break;
                }
            }
        },
        data() {
            return {
                step: 1,
                infoOderMeldung: ['Ich brauche Informationen', 'Ich will einen Verdacht melden'],
                infoOderMeldungA: '',
                showInfoBox: false,
                risikogebiete: ['Italien', 'Frankreich', 'China', 'Iran'],
                risikogebieteA: [],
                kontakt: ['ja', 'nein', 'nicht sicher'],
                kontaktA: '',
                wahrscheinlichkeit: 0,
                symptome: ['Fieber', 'trockener Husten', 'Atembeschwerden', 'Abgeschlagenheit'],
                symptomeA: [],
                eigenesRisiko: ['Über 60 Jahre alt', 'chronische Atemwegserkrankung (Asthma, COPD', 'andere chronische Erkrankung'],
                eigenesRisikoA: [],
                fremdgefaehrdung: ['Ich pflege Angehörige', 'Ich habe kleine Kinder', 'Ich arbeite im Gesundheitswesen/ Pflege/ Betreuung'],
                fremdgefaehrdungA: []
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
