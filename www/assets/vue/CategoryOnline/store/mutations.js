import camelCaseKeys from 'camelcase-keys'

import _ from 'lodash'
import Vue from 'vue';

export default {
    setCategoryId(state, category) {
        state.category_id = category;
    },
    setMatches(state, matches) {
        state.matches = matches
    },
    setMatch(state, match) {
        Vue.set(state.matches, match.id, match);
    },
    setBaseLogosPath(state, path) {
        state.baseLogosPath = path;
    },

    pushSocketQueue(state, obj) {
        state.socket.queue.push(obj);
    },

    SOCKET_ONOPEN(state, event) {
        state.socket.isConnected = true;
        let obj;
        while (obj = state.socket.queue.pop()) {
            this.$socket.sendObj(obj);
        }
    },
    SOCKET_ONCLOSE(state, event) {
        state.socket.isConnected = false;
    },
    SOCKET_ONERROR(state, event) {
        state.socket.isConnected = false;

    },
    // default handler called for all methods
    SOCKET_ONMESSAGE(state, data) {
        data = camelCaseKeys(data);
        state.lastData = data;

        if (_.has(data, 'matches')) {
            this.commit('setMatches', data.matches);
        }
        if (_.has(data, 'match')) {
            this.commit('setMatch', data.match);
        }
    },
    // mutations for reconnect methods
    SOCKET_RECONNECT(state, count) {
        // attempt to reconnect
        state.socket.reconnectionCount = count;
    },
    SOCKET_RECONNECT_ERROR(state) {
        state.socket.reconnectError = true;
    },
};