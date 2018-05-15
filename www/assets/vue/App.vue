<template>
    <div>
        <transition name="load">
            <template v-if="match.id && events">
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
        </transition>

        <vue-loading
                v-if="!(match.id && events)"
                type="spin" color="#0e5eff"
                :size="{ width: '100px', height: '100px' }"
        ></vue-loading>
    </div>
</template>

<script>
    import MatchHeader from './components/MatchHeader'
    import FacebookVideo from './components/FacebookVideo'
    import EventList from './components/EventList'
    import VueLoading from 'vue-loading-template'


    export default {
        name: "App",
        components: {
            MatchHeader,
            FacebookVideo,
            EventList,
            VueLoading
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
    .load-enter-active, .load-leave-active {
        transition: opacity .5s;
    }

    .load-enter, .load-leave-to {
        opacity: 0;
    }
</style>