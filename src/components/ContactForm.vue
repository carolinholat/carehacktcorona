<template>
    <div>
        <h2>User Management</h2><br>
        <EditUser style="align-content: space-around ; margin: 20px 20px" v-bind:person="person" />
        <span v-bind:key="person" v-for="person in persons">
            <ShowUser style="align-content: space-around; margin: 20px 20px" v-bind:person="person"/>

         </span>
    </div>
</template>

<script>
    import eventBus from './../main.js';
    import ShowUser from "./Users/ShowUser";
    import EditUser from "./Users/EditUser";

    export default {
        name: "ContactForm",
        components: {EditUser, ShowUser},
        data() {
            return {
                number_of_persons: 2,
                persons: [
                    'anna',
                    'berta'
                ]
            }
        },
        methods: {
            addPerson: function(Array) {
               // this.number_of_persons +=1;
                let array = this.persons;
                array.push(Array);
                //this.persons.push(Array);
                alert(Array);


            }
        },
        computed: {
            calculator_ingredients: function() {
                let vue_instance = this;
                let name_calculator = vue_instance.name; // ingredients ist in Calculator.vue drin
                return name_calculator;
            }
        },
        created() {
            eventBus.$on('addPerson', (param) => {
              this.persons.push(param);
                alert(param);}
            );
        }
    }
</script>

<style scoped>
    div {
        margin-left: auto;
        margin-right: auto;
    }

    span {
        display: flex;
    }
</style>