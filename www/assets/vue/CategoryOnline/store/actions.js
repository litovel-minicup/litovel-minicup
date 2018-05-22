import Vue from "vue";

export default {
    subscribe({commit, dispatch, state}, category=state.category_id) {
        console.log(category);
        commit('setCategoryId', category);
        dispatch('sendObj', {
            action: 'subscribe_category',
            category
        });
    },
    /*refreshFallback({state, commit}) {
        Vue.http.get('/api/v1/match/detail/' + state.match_id).then(response => {
            commit('setMatch', response.body.match)
        }, response => {
            // TODO: errro
        });
    },*/


    sendObj({state, commit}, obj) {
        console.log('Socket message', obj);
        if (state.socket.isConnected) {
            this.$socket.sendObj(obj)
        } else {
            commit('pushSocketQueue', obj)
        }
    },
}