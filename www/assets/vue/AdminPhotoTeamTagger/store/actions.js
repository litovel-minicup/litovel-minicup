import Vue from "vue";
import axios from "axios";

export default {
    loadPhotos({state, commit}) {
        return Vue.http.get(state.photosUrl).then(({data}) => {
            commit('setPhotos', data.photos);
        });
    },
}