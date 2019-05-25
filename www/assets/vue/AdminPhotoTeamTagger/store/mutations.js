import _ from 'lodash'
import Vue from 'vue';

export default {
    setConf(state, conf) {
        state.updateTagsUrl = conf.updateTagsUrl;
        state.photosUrl = conf.photosUrl;
        state.insertPhotosUrl = conf.insertPhotosUrl;
        state.deletePhotosUrl = conf.deletePhotosUrl;
    },

    setPhotos(state, photos) {
        state.photos = photos;
    }
};