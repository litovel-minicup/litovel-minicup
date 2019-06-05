import Vuex from 'vuex';
import Vue from "vue";
import actions from './actions'
import mutations from './mutations'
import _ from "lodash";

Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        addTeamTagUrl: '',
        photosUrl: '',
        teamsUrl: '',
        photos: [],
    },
    actions,
    mutations,
    getters: {
    },
    strict: true
});


export default store