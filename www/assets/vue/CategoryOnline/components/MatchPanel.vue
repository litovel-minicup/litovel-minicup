<template>
    <a v-if="full" href="" class="Live__video">
        <div class="Live__video__teams">
            <div class="Live__video__team">
                <div class="Live__video__team__img">
                    <img :src="`${baseLogosPath}${match.home_team_slug}.png`" alt="">
                </div>
                <h3>{{ match.home_team_name }}</h3>
            </div>

            <div class="Live__video__team">
                <div class="Live__video__team__img">
                    <img :src="`${baseLogosPath}${match.away_team_slug}.png`" alt="">
                </div>
                <h3>{{ match.away_team_name }}</h3>
            </div>
        </div>

        <div class="Live__video__score">
            <div class="Live__video__score__now">
                <span>{{ match.state | onlineStateName }} {{ time }}</span>
                <span v-show="playing"></span>
            </div>
            <h2>{{ match.score[0] | score }}:{{ match.score[1] | score }}</h2>
            <span>Zobrazit zápas</span>
        </div>
    </a>
    <a v-else href="#">
        <h4>{{ match.home_team_name }}</h4>
        <h4>{{ match.away_team_name }}</h4>
        <span>Zobrazit zápas ({{ new Date(match.match_term_start*1000) }})</span>
    </a>
</template>

<script>
    import _ from 'lodash';
    import {mapState} from 'vuex';

    export default {
        name: "MatchPanel",
        props: {
            match: {
                required: true,
                type: Object,
            },
            full: {
                required: false,
                default: false,
                type: Boolean,
            }
        },
        data() {
            return {
                timerCount: 0,
                timerID: 0
            }
        },
        filters: {
            score(val) {
                if (val === null) return '-';
                return val;
            },
            logoPath(slug) {
                return `${this.baseLogosPath}${slug}.png`;
            }
        },
        computed: {
            time() {
                if (this.playing)
                    return `(${Math.floor(this.timerCount / 60).pad(2)}:${(this.timerCount % 60).pad(2)})`;
            },
            playing() {
                return _.includes(['half_first', 'half_second'], this.match.state);
            },
            ...mapState(['baseLogosPath'])
        },
        created() {
            this.timerID = setInterval(() => {
                const start = this.match.second_half_start ? this.match.second_half_start : this.match.first_half_start;
                this.timerCount = (Number(Date.now() / 1000) - start) | 0;
            }, 1000);
        },
        destroyed() {
            clearInterval(this.timerID);
        }
    }
</script>

<style scoped>

</style>