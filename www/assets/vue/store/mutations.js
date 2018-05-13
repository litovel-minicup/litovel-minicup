import camelCaseKeys from 'camelcase-keys'

import _ from 'lodash'

export default {
    setMatch(state, match) {
        state.match = match;
    },
    setEvents(state, events) {
        state.events = events
    },
    addEvent(state, event) {
        state.events.unshift(event)
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

        if (_.has(data, 'event')) {
            this.commit('addEvent', data.event);
        }

        if (_.has(data, 'match')) {
            this.commit('setMatch', data.match);
        }
    },
    // mutations for reconnect methods
    SOCKET_RECONNECT(state, count) {
        // attempt to reconnect
    },
    SOCKET_RECONNECT_ERROR(state) {
        state.socket.reconnectError = true;
    },
};