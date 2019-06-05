import _ from 'lodash'
import Vue from 'vue';

export default {
    setConf(state, conf) {
        state.updateTagsUrl = conf.updateTagsUrl;
        state.photosUrl = conf.photosUrl;
        state.insertPhotosUrl = conf.insertPhotosUrl;
        state.addTeamTagUrl = conf.addTeamTagUrl;
        state.deletePhotosUrl = conf.deletePhotosUrl;
        state.teamsUrl = conf.teamsUrl;
    },

    setPhotos(state, photos) {
        state.photos = _.sortBy(photos, [(p) => p.taken]);
    },
    setTeams(state, teams) {
        state.teams = teams;
    },
    setHistogram(state, {id, histogram}) {
        _.find(state.teams, {id}).color_histogram = histogram;
    },
};