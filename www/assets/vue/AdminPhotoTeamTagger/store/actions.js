import Vue from "vue";
import axios from "axios";

export default {
    loadPhotos({state, commit}) {
        return Vue.http.get(state.photosUrl).then(({data}) => {
            commit('setPhotos', data.photos);
        });
    },
    loadTeams({state, commit}) {
        return Vue.http.get(state.teamsUrl).then(({data}) => {
            commit('setTeams', data.teams);
        });
    },
    uploadTeams({state, commit}) {
        return Vue.http.post(state.teamsUrl, {teams: state.teams}).then(({data}) => {

        });
    },
    addTeamTag({state, commit}, {team, photo}) {
        return Vue.http.post(state.addTeamTagUrl, {team, photo}).then(({data}) => {

        });
    },

    findBestMatches({state, commit}, {histogram}) {
        histogram = JSON.parse(JSON.stringify(histogram));
        const matches = _.orderBy(_.map(state.teams, (t) => {

            // console.info(t.name, t.color_histogram, histogram, _.intersectionWith(t.color_histogram, histogram, _.isEqual).length);

            return [t, _.intersectionWith(t.color_histogram, histogram, _.isEqual)]

        }), [_.property([1, 'length'])], ['desc']);
        return _.slice(matches, 0, 3);


    },
}