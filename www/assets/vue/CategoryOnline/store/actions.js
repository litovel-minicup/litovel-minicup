import Vue from "vue";

export default {
    subscribe({commit, dispatch, state}, category=state.category_id) {
        commit('setCategoryId', category);
        dispatch('sendObj', {
            action: 'subscribe_category',
            category
        });
    },
    subscribeFallback({state, commit}) {
        Vue.http.get('/api/v1/category/upcoming-matches/' + state.category_id).then(response => {
            commit('setMatches', response.body.matches)
        }, response => {
            // TODO: errro
        });
    },

    sendObj({state, commit}, obj) {
        if (state.socket.isConnected) {
            this.$socket.sendObj(obj)
        } else {
            commit('pushSocketQueue', obj)
        }
    },
}