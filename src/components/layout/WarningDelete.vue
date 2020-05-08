<template>
    <v-overlay
            absolute="absolute"
            :z-index="2000"
    >
        <v-card grid-list-md text-xs-center style="min-width: 200px; margin: auto; margin-top: 150px; padding: 10px">

            <v-card-text align="center" style="width: 80%; margin: auto; padding: 30px">
                Sicher, dass Sie mit dem Löschen fortfahren möchten?
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions class="justify-center">
                <v-spacer></v-spacer>
                <v-btn
                        class="warning justify-center"
                        text
                        @click="$emit('loeschen')"
                >
                    Löschen
                </v-btn>
                <v-btn
                        class="success justify-center"
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
    export default {
        name: "WarningDelete"
    }
</script>

<style scoped>

</style>