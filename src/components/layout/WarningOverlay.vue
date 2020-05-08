<template>
    <v-overlay
            absolute="absolute"
            :z-index="2000"
    >
        <v-btn
                color="primary"
                @click="$emit('weiter')"
        >
            {{msg}}
        </v-btn>
    </v-overlay>
</template>

<script>
    export default {
        props: {
          msg: String
        },
        name: "WarningOverlay"
    }
</script>

<style scoped>

</style>