<template>
    <div>
        Online!
    </div>
</template>

<script>
    export default {
        name: "App",
        data() {
            return {
                homeTeamUrl: '',
                awayTeamUrl: '',
                matchId: 0
            }
        },
        computed: {},
        methods: {},
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.matchId = data.matchId;
            this.homeTeamUrl = data.homeTeamUrl;
            this.awayTeamUrl = data.awayTeamUrl;


        }
    }
</script>

<style scoped>

</style>