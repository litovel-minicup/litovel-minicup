import Vue from "vue";
import axios from "axios";

export default {
    loadMainTags({state, commit}) {
        return Vue.http.get('/api/v1/main-tags').then(({data}) => {
            commit('setMainTags', data.tags);
        });
    },
    updateTags({state, commit}, {tags, photos}) {
        return Vue.http.post(state.updateTagsUrl, {tags, photos}).then(({data}) => {
            commit('refreshTagsCount', data.tags);
        })
    },
    insertPhotos({state, commit}, photos) {
        return Vue.http.post(state.insertPhotosUrl, {photos}).then(({data}) => {
            commit('rejectPhotos', photos);
        })
    },
    deletePhotos({state, commit}, photos) {
        return Vue.http.post(state.deletePhotosUrl, {photos}).then(({data}) => {
            commit('rejectPhotos', photos);
        })
    },
    refreshPhotos({state, commit}) {
        return axios.get(state.photosUrl).then((r) => {
            commit('setPhotos', r.data.photos);
        })
    },
}