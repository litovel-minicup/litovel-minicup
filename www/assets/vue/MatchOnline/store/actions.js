import Vue from "vue";

export default {
    loadMatch({commit, dispatch}, {match}) {
        commit('setMatchId', match);
        dispatch('subscribe', match);
    },
    sendObj({state, commit}, obj) {
        console.log('Socket message', obj);
        if (state.socket.isConnected) {
            this.$socket.sendObj(obj)
        } else {
            commit('pushSocketQueue', obj)
        }
    },
    subscribe({state, commit, dispatch}, {match}) {
        commit('setMatchId', match);
        dispatch('sendObj', {
            action: 'subscribe',
            match
        });
    },
    refreshFallback({state, commit}) {
        Vue.http.get('/api/v1/match/detail/' + state.match_id).then(response => {
            commit('setMatch', response.body.match)
        }, response => {
            // TODO: errro
        });
    },
    loadEvents({state, commit}) {
        Vue.http.get('/api/v1/match/events/' + state.match_id).then(response => {
            commit('setEvents', response.body.events)
        }, response => {
            // TODO: errro
        });
    }
}