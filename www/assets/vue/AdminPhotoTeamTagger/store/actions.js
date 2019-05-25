import Vue from "vue";
import axios from "axios";

export default {
    loadImages({state, commit}) {
        return Vue.http.get(state.photosUrl).then(({data}) => {
            commit('setPhotos', data.photos);
        });
    },
}