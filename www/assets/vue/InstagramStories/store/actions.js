import Vue from "vue";
import axios from 'axios'

export default {
    loadStories({commit, dispatch}, username) {
        axios.get('/api/v1/instagram-stories').then(({data}) => {
            commit('setStories', data.stories);
        });
    },
}