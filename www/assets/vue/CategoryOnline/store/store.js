import Vuex from 'vuex';
import Vue from "vue";
import actions from './actions'
import mutations from './mutations'

Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        lastData: null,
        socket: {
            isConnected: false,
            reconnectError: false,
            queue: [],
            reconnectionCount: 0,
        },
        category_id: 0,
        matches: {},

        baseLogosPath: null,
        matchDetailUrlPattern: null,
        serverTimeOffset: 0,
    },
    actions,
    mutations,
    strict: true
});


export default store