import _ from 'lodash'
import Vue from 'vue';

export default {
    setConf(state, conf) {
        state.updateTagsUrl = conf.updateTagsUrl;
        state.photosUrl = conf.photosUrl;
        state.insertPhotosUrl = conf.insertPhotosUrl;
        state.deletePhotosUrl = conf.deletePhotosUrl;
    },
    setTags(state, tags) {
        state.tags = tags;
    },
    setPhotos(state, photos) {
        state.photos = photos;
    },
    setSelectedPhotos(state, photos) {
        state.selectedPhotos = photos;
    },
    setLastSelectedPhoto(state, photo) {
        state.lastSelectedPhoto = photo;
    },
    setSelectedTags(state, tags) {
        state.selectedTags = tags;
    },
    setAnotherTags(state, anotherTags) {
        state.anotherTags = anotherTags;
    },
    selectMultiplePhotos(state, {from, to}) {
        let toAdd = _.map(_.slice(state.photos, from, to + 1), _.property('id'));
        state.selectedPhotos = _.uniq([...state.selectedPhotos, ...toAdd]);
    },

    updateTags(state) {
        let photosToUpdate = state.selectedPhotos.length ? state.selectedPhotos : _.map(_.keys(state.photos), Number);
        let anotherTags = _.map(state.anotherTags, _.property('id'));
        let tags = _.uniq(_.concat(state.selectedTags, anotherTags));

        photosToUpdate.map((p) => {
            p = _.find(state.photos, {'id': p});
            p.tags = _.union(p.tags, tags);
        })
    },
    rejectPhotos(state, photos) {
        state.photos = _.filter(state.photos, (p) => {
            return !photos.includes(p.id);
        });
        state.selectedPhotos = _.filter(state.selectedPhotos, (p) => {
            return !photos.includes(p);
        })
    },
};