<template>
    <div>
        <v-text-field
                v-model="search"
                append-icon="mdi-magnify"
                label="Suche"
                single-line
                hide-details
        ></v-text-field>
        <v-data-table
                      :headers="headers"
                      :items="itemArray"
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
                            <v-btn @click="$emit('showUpdateItem', item.ID)">
                                BEARBEITEN
                            </v-btn>
                        </template>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    export default {
        name: "ItemDatatable",
        props: {
            headers: Array,
            itemArray: Array
        },
        data() {
            return {
                search: ''
            }
        }
    }
</script>

<style scoped>

</style>