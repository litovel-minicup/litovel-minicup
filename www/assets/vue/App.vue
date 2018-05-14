<template>
    <div>
        <match-header></match-header>
        <event-list :events="events" :match="match"></event-list>
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
            const matchId = this.$root.$el.parentElement.dataset.matchId;
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