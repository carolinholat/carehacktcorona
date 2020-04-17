<template>
    <div>
        <h2>Edit user</h2>
        <v-card
                max-width="344"
                class="mx-auto"
                margin-top="margin-top"
                @mousemove.stop=""
        >
            <h3><input type="text" v-model="temporary_name"/></h3>
            <h3><input type="submit" value="Submit" @click="changeProps"/></h3>

        </v-card>
    </div>
</template>

<script>
    import eventBus from '../../main.js';
    export default {
        name: "EditUser",
        props: ["person"],
        data() {
            return {
                temporary_name: "",
                temporary_age: '',
                temporary_jobtitle: ''
            }
        },
        methods: {
            changeProps: function() {
                eventBus.$emit('addPerson', this.temporary_name);
               // this.$emit('addPerson', Array);
            }
        },
       computed: {

       }
    }
</script>

<style scoped>

</style>