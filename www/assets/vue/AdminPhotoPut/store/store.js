import Vuex from 'vuex';
import Vue from "vue";
import actions from './actions'
import mutations from './mutations'
import _ from "lodash";

Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        updateTagsUrl: '',
        insertPhotosUrl: '',
        deletePhotosUrl: '',
        photosUrl: '',

        photos: {},
        tags: [],

        selectedPhotos: [],
        selectedTags: [],
        anotherTags: [],
    },
    actions,
    mutations,
    getters: {
        photosToUpdate(state) {
            return state.selectedPhotos.length ? state.selectedPhotos : _.map(_.keys(state.photos), Number);
        }
    },
    strict: true
});


export default store