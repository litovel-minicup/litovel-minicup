<template>
    <div class="MatchDetail__head">
        <h2 class="Article__heading"><a :href="homeTeamUrl">{{ match.home_team_name }}</a></h2>
        <span class="MatchDetail__head__score">{{ match.score[0] | score }}:{{ match.score[1] | score }}</span>
        <h2 class="Article__heading"><a :href="homeTeamUrl">{{ match.away_team_name }}</a></h2>
    </div>
</template>

<script>
    import {mapState} from 'vuex'

    export default {
        name: "MatchHeader",
        props: ['homeTeamUrl', 'awayTeamUrl'],
        computed: mapState([
            'match',
        ]),
        filters: {
            score(v) {
                return v == null ? 0 : v;
            }
        }
    }
</script>

<style scoped>

</style>