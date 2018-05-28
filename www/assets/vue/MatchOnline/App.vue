<template>
    <div>
        <transition name="load">
            <template v-if="match.id">
                <div>
                    <match-header
                            :home-team-url="homeTeamUrl"
                            :away-team-url="awayTeamUrl"
                    ></match-header>
                    <facebook-video
                            v-if="facebookVideoId"
                            :facebook-video-id="facebookVideoId"
                    ></facebook-video>
                    <event-list
                            :events="events"
                            :match="match"
                    ></event-list>
                </div>
            </template>
        </transition>

        <vue-loading
                v-if="!match.id"
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
    import {mapState} from 'vuex'


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
                matchId: 0
            }
        },
        computed: {
            ...mapState({
                facebookVideoId: state => state.match.facebook_video_id,
                match: 'match',
                events: 'events'
            }),
            fallbackReloadInterval() {
                // if match is playing, reload interval is 30 reconnect counts, else 60
                return ['half_first', 'half_second'].indexOf(this.match.state) >= 0 ? 30 : 60
            }
        },
        methods: {
            loadMatch() {
                this.$store.dispatch('subscribe', {match: this.matchId});
                this.$store.dispatch('loadEvents');
            },
            refreshFallback() {
                this.$store.dispatch('refreshFallback');
                this.$store.dispatch('loadEvents');
            }
        },
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.matchId = data.matchId;
            this.homeTeamUrl = data.homeTeamUrl;
            this.awayTeamUrl = data.awayTeamUrl;

            this.loadMatch();

            !this.$store.state.socket.isConnected && this.refreshFallback();

            // plan refresh after connection lost
            this.$store.watch(
                (state) => {
                    return state.socket.isConnected;
                },
                (old, new_) => {
                    // load match after connect
                    !old && new_ && this.loadMatch();
                }
            );
            this.$store.watch(
                (state) => {
                    return state.socket.reconnectionCount;
                },
                (old, new_) => {
                    // use load fallback every 10 reconnect fails (include first attempt)
                    !(new_ % this.fallbackReloadInterval) && this.refreshFallback();
                }
            );
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