import Vue from "vue";
import axios from 'axios'
import _ from 'lodash'

export default {
    loadNews({commit, dispatch, state}) {
        Vue.http.get('/api/v1/news-live-bar/' + state.category_id).then(response => {
            commit('setNews', _.values(response.body.news))
        }, response => {
            // TODO: errro
        });
    },
    subscribe({commit, dispatch, state}, category=state.category_id) {
        commit('setCategoryId', category);
        dispatch('sendObj', {
            action: 'subscribe_category',
            category
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