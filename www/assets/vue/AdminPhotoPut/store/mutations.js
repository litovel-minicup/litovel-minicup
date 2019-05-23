import _ from 'lodash'
import Vue from 'vue';

export default {
    setConf(state, conf) {
        state.updateTagsUrl = conf.updateTagsUrl;
        state.photosUrl = conf.photosUrl;
        state.insertPhotosUrl = conf.insertPhotosUrl;
        state.deletePhotosUrl = conf.deletePhotosUrl;
    },
    setMainTags(state, tags) {
        state.mainTags = tags;
    },
    setPhotos(state, photos) {
        state.photos = photos;
    },
    refreshTagsCount(state, tags) {
        tags.map(({id, tags}) => {
            _.find(state.photos, {id}).tags = tags;
        })
    },
    rejectPhotos(state, photos) {
        state.photos = _.filter(state.photos, (p) => {
            return !photos.includes(p.id);
        })
    },
};