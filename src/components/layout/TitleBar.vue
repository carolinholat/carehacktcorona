<template>
    <div>
        <v-toolbar style="height: 100px;"  :color="checkLogin">
            <!--#ADD8E6  #1E90FF-->
            <!--<div class="flex-grow-1"></div>

            <div>
                <v-btn text>
                    <router-link to="/" class="routerLink">Home</router-link>
                </v-btn>
                <v-btn text>
                    <router-link to="/input" class="routerLink">Input</router-link>
                </v-btn>
                <v-btn text>
                    <router-link to="/infos" class="routerLink">Infos</router-link>
                </v-btn>

                <v-spacer></v-spacer>
                <v-btn text>
                    <router-link to="/login" class="routerLink">Login</router-link>
                </v-btn>
            </div> -->
            <v-btn icon style="margin-left: 50px">
                <router-link to="/" class="routerLink">
                    <img src="../../../public/LogoSCIO_care.png"
                         style="height: 100px; width: 120px; margin-top: 10px; padding-top: 20px"/>
                </router-link>
            </v-btn>

            <v-toolbar-title style="margin-left: 50px; color: black"
                             justify="center"
                             class="hidden-sm-and-down"
            >
                Pro-Juventa Kommunikation
            </v-toolbar-title>

            <v-spacer></v-spacer>
            <v-btn icon style="margin-right: 70px" v-if="$store.state.admin && $store.state.token !== ''">
                <v-btn text>
                    <router-link to="/input" class="routerLink">DASHBOARD</router-link>
                </v-btn>
            </v-btn>
            <v-btn icon style="margin-right: 50px" v-if="$store.state.token !== ''">
                <v-btn text>
                    <router-link to="/profil" class="routerLink">MEIN PROFIL</router-link>
                </v-btn>
            </v-btn>

            <v-btn icon style="margin-right: 50px" justify="center" @click="logout()">
                <v-btn text>
                    <router-link to="/login" class="routerLink">{{$store.state.token === '' ? 'LOGIN' : 'LOGOUT'}}
                    </router-link>
                </v-btn>
            </v-btn>

            <v-btn icon style="margin-right: 50px">
                <v-btn text>
                    <router-link to="/feedback" class="routerLink">FEEDBACK</router-link>
                </v-btn>
            </v-btn>
            <v-app-bar-nav-icon
                    class="hidden-md-and-up"
                    @click="drawer = !drawer"
            />

        </v-toolbar>
        <v-toolbar style="background: whitesmoke">
            <v-menu offset-y>
                <!--
                <template v-slot:activator="{ on }" >
                    <v-btn style="margin-left: 40px"
                            color="primary"
                            dark
                            v-on="on"
                    >
                        Themen
                    </v-btn>
                </template> -->
                <v-list>
                    <v-list-item
                            v-for="item in ['bli', 'bla']"
                            :key="item"
                    >
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
            <v-spacer></v-spacer>
            <v-btn style="margin: 20px"
                   color="primary">
                <router-link to="/forum" class="routerLink">Forum</router-link>
            </v-btn>
            <v-btn style="margin: 20px"
                   color="primary">
                <router-link to="/infos" class="routerLink">FAQ</router-link>
            </v-btn>
            <v-autocomplete
                    class="hidden-sm-and-down"
                    icon style="margin-right: 50px; max-width: 20% !important; margin-top: 20px;"
                    :items="itemsSuche"
                    :search-input.sync="search"
                    hide-no-data
                    hide-selected
                    label="Suche"
                    placeholder="Ihr Suchbegriff"
                    prepend-icon="mdi-magnify"
                    return-object
                    @input="toMeldung($event.value)"
            ></v-autocomplete>


        </v-toolbar>
        <Drawer
                v-model="drawer"
        />
    </div>
</template>

<script>
    import Drawer from './Drawer'
    export default {
        props: {
            itemsSuche: Array
        },
        components: {
          Drawer
        },
        name: "TitleBar",
        data() {
            return {
                search: '',
                drawer: null
            }
        },
        methods: {
            logout() {
                this.$store.commit('setToken', '');
                this.$store.commit('setAbo', []);
                this.$store.commit('setAboFix', []);
                this.$store.commit('setAboFlex', []);
                this.$store.commit('setAbteilung', 0);
                this.$store.commit('setKategorien', []);
                this.$store.commit('unsetAdmin');
            },
            toMeldung(link) {
                if(this.$route !== 'infos') {
                    this.$router.push({path: 'infos?id=' + link});
                } else {
                    this.$emit('showMeldung', link);
                }
            }
        },
        computed: {
            checkLogin() {
                if (this.$store.state.token === '') {
                    return '#87ceeb';
                }
                else {
                    return /*'#a5d6a7' */  '#188600';
                }
            }
        }
    }
</script>

<style scoped>
    .routerLink {
        text-decoration: none;
        color: black;
        /*color: whitesmoke; */
    }

    .routerLink:hover {
        text-decoration: none;
        /* color: yellow; */
        color: #0d47a1;
    }


</style>