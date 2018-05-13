<template>
    <div>
        <match-header></match-header>
    </div>
</template>

<script>
    import MatchHeader from './components/MatchHeader'

    export default {
        name: "App",
        props: ['match-id'],
        components: {
            MatchHeader
        },
        mounted() {
            const matchId = this.$root.$el.parentElement.dataset.matchId;
            this.$store.dispatch('subscribe', {match: matchId});
        }
    }
</script>

<style scoped>

</style>