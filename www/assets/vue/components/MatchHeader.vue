<template>
    <div class="MatchDetail__head">
        <h2 class="Article__heading">{{ match.home_team_name }}</h2>
        <span class="MatchDetail__head__score">{{ match.score[0] | score }}:{{ match.score[1] | score }}</span>
        <h2 class="Article__heading">{{ match.away_team_name }}</h2>
    </div>
</template>

<script>
    import {mapState} from 'vuex'

    export default {
        name: "MatchHeader",
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