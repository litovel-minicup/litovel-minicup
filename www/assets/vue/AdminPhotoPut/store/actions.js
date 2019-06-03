import Vue from "vue";
import axios from "axios";

export default {
    loadTags({state, commit}) {
        return Vue.http.get('/api/v1/tags').then(({data}) => {
            commit('setTags', _.values(data.tags));
        });
    },
    insertPhotos({state, commit, getters}) {
        let photos = getters.photosToUpdate;
        let data = photos.map((id) => {
            return {
                id,
                tags: _.find(state.photos, {id}).tags
            }
        });
        return Vue.http.post(state.insertPhotosUrl, {photos: data}).then(({data}) => {
            commit('rejectPhotos', photos);
        })
    },
    deletePhotos({state, commit, getters}) {
        let photos = getters.photosToUpdate;
        return Vue.http.post(state.deletePhotosUrl, {photos}).then(({data}) => {
            commit('rejectPhotos', photos);
        })
    },
    refreshPhotos({state, commit}) {
        return axios.get(state.photosUrl).then((r) => {
            commit('setPhotos', r.data.photos);
        })
    },

    toggleTag({state, commit}, id) {
        if (state.selectedTags.includes(id))
            commit('setSelectedTags', _.reject(state.selectedTags, (p) => p === id));
        else
            commit('setSelectedTags', [...state.selectedTags, id]);

    },
    togglePhoto({state, commit}, id) {
        if (state.selectedPhotos.includes(id)) {
            commit('setSelectedPhotos', _.reject(state.selectedPhotos, (p) => p === id));
        } else {
            commit('setSelectedPhotos', [...state.selectedPhotos, id]);
            commit('setLastSelectedPhoto', id);

        }
    },
    selectMultiplePhotos({state, commit}, id) {
        if (state.lastSelectedPhoto) {
            let fromIdx = _.findIndex(state.photos, {id: state.lastSelectedPhoto});
            let toIdx = _.findIndex(state.photos, {id});

        }
    }
}