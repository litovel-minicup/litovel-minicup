<template>
    <div>
        <match-header></match-header>
        <facebook-video
                v-if="facebookVideoId"
                :facebook-video-id="facebookVideoId"
        ></facebook-video>
    </div>
</template>

<script>
    import MatchHeader from './components/MatchHeader'
    import FacebookVideo from './components/FacebookVideo'

    export default {
        name: "App",
        components: {
            MatchHeader,
            FacebookVideo
        },
        computed: {
            facebookVideoId() {
                return this.$store.state.match.facebook_video_id
            }
        },
        mounted() {
            const matchId = this.$root.$el.parentElement.dataset.matchId;
            this.$store.dispatch('subscribe', {match: matchId});
        }
    }
</script>

<style scoped>

</style>