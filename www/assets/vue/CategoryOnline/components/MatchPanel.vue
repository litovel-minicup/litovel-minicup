<template>
    <a v-if="full" :href="matchDetailUrl" class="Live__video" :data-match-id="match.id">
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
    <a v-else :href="matchDetailUrl" :data-match-id="match.id">
        <h4>{{ match.home_team_name }}</h4>
        <h4>{{ match.away_team_name }}</h4>
        <h5>{{ matchTermStart }}</h5>
        <span>Zobrazit zápas</span>
    </a>
</template>

<script>
    import _ from 'lodash';
    import {sprintf} from 'sprintf-js';
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
            },
        },
        computed: {
            ...mapState(['baseLogosPath', 'matchDetailUrlPattern', 'serverTimeOffset']),
            matchTermStart() {
                const d = new Date(this.match.match_term_start * 1000);
                const time = `${d.getHours().pad(2)}:${d.getMinutes().pad(2)}`;
                if (d.toDateString() === new Date().toDateString()) return time;
                return `${d.getDate()}.${d.getMonth() + 1}.${d.getFullYear()} ${time}`;
            },
            matchDetailUrl() {
                // corresponding with Match::MATCH_DETAIL_FULL_URL_PATTERN
                return sprintf(
                    this.matchDetailUrlPattern,
                    this.match.home_team_slug,
                    this.match.away_team_slug,
                    this.match.year_slug,
                    this.match.category_slug,
                );
            },
            time() {
                if (this.playing)
                    return `(${Math.floor(this.timerCount / 60).pad(2)}:${(this.timerCount % 60).pad(2)})`;
            },
            playing() {
                return _.includes(['half_first', 'half_second'], this.match.state);
            },
        },
        created() {
            this.timerID = setInterval(() => {
                const start = this.match.second_half_start ? this.match.second_half_start : this.match.first_half_start;
                this.timerCount = Math.floor(Number(Date.now() / 1000) - start + this.serverTimeOffset);
            }, 1000);
        },
        destroyed() {
            clearInterval(this.timerID);
        }
    }
</script>

<style scoped>

</style>