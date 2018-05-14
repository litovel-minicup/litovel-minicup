<template>
    <div>
        <match-header
                :home-team-url="homeTeamUrl"
                :away-team-url="awayTeamUrl"
        ></match-header>
        <event-list
                :events="events"
                :match="match"
        ></event-list>
        <facebook-video
                v-if="facebookVideoId"
                :facebook-video-id="facebookVideoId"
        ></facebook-video>
    </div>
</template>

<script>
    import MatchHeader from './components/MatchHeader'
    import FacebookVideo from './components/FacebookVideo'
    import EventList from './components/EventList'

    export default {
        name: "App",
        components: {
            MatchHeader,
            FacebookVideo,
            EventList
        },
        data() {
            return {
                homeTeamUrl: '',
                awayTeamUrl: '',
            }
        },
        computed: {
            facebookVideoId() {
                return this.$store.state.match.facebook_video_id
            },
            // TODO: vuex map state
            events() {
                return this.$store.state.events
            },
            match() {
                return this.$store.state.match
            },
        },
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            const matchId = data.matchId;
            this.homeTeamUrl = data.homeTeamUrl;
            this.awayTeamUrl = data.awayTeamUrl;

            this.$store.dispatch('subscribe', {match: matchId});
            this.$store.dispatch('loadEvents');
            setTimeout(() => {
                if (!this.$store.state.socket.isConnected) {
                    this.$store.dispatch('loadMatchFallback');
                }
            }, 500); // TODO: fallback timer
        }
    }
</script>

<style scoped>

</style>