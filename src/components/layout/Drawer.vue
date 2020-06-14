<template>
    <v-navigation-drawer
            bottom
            color="transparent"
            fixed
            height="auto"
            overlay-color="secondary"
            overlay-opacity=".8"
            temporary
            v-bind="$attrs"
            v-on="$listeners"
    >
        <v-list
                color="white"
                shaped
        >
            <v-list-item
                    v-for="item in items"
                    v-if="showItemIfAllowed(item)"
                    :key="item.name"
                    :to="item.link "
                    :exact="item.text === 'Home'"
                    color="primary"
            >
                <v-list-item-content>
                    <!-- <v-list-item-title v-text="name" /> -->
                    <v-btn>{{item.text}}</v-btn>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
    export default {
        name: 'Drawer',

        /* props: {
           items: {
             type: Array,
             default: () => ([]),
           },
         }, */
        data() {
            return {
                items: [{text: 'Login', link: 'login'}, {text: 'Profil', link: 'profil'},
                    {text: 'Dashboard', link: 'dashboard'}, {text: 'Feedback', link: 'feedback'}]
            }
        },
        methods: {
            showItemIfAllowed(item) {
                if ((item.link !== 'profil' && item.link !== 'dashboard' )
                    || (item.link === 'profil' && this.$store.state.token !== '')
                    || (item.link === 'dashboard' && this.$store.state.token !== '' && this.$store.state.admin)
                ) {
                   return true;
                }
                else return false;
            }
        }
    }
</script>
