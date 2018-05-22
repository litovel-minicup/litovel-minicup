<template>
    <div>
        <div class="Box__content Live">
            <match-panel
                    v-for="(match, match_id) in onlineMatches"
                    :key="match.id"
                    :match="match"
                    :full="true"
            ></match-panel>
        </div>
        <template v-if="upcomingMatches.length > 0">
            <div v-if="" class="Article__head Box__head">
                <h2 class="Article__head__text Box__head__text">Další zápasy</h2>
            </div>
            <div class="Box__content Live">
                <nav>
                    <match-panel
                            v-for="(match, match_id) in upcomingMatches"
                            :key="match.id"
                            :match="match"
                            :full="false"
                    ></match-panel>
                </nav>
            </div>
        </template>
    </div>
</template>

<script>
    import MatchPanel from "./MatchPanel";
    import {mapActions, mapState} from 'vuex';
    import _ from 'lodash';


    export default {
        name: "CategoryOnline",
        components: {
            MatchPanel
        },
        data() {
            return {
                timerID: 0
            }
        },
        methods: mapActions(['subscribe']),
        computed: {
            ...mapState(['matches']),
            onlineMatches() {
                return _.pickBy(this.matches, (match, _) => {
                    return this.$options.filters.isOnline(match);
                });
            },
            upcomingMatches() {
                return _.pickBy(this.matches, (match, _) => {
                    return !this.$options.filters.isOnline(match);
                });
            }
        },
        filters: {
            isOnline(match) {
                return _.includes(['half_first', 'pause', 'half_second'], match.state);
            },
            not(v) {
                return !v
            },
            isNotEmpty(v) {
                return !_.isEmpty(v)
            }
        },
        created() {
            this.timerID = setInterval(() => {
                this.subscribe()
            }, 1000 * 15);
        },
        destroyed() {
            clearInterval(this.timerID);
        }
    }
</script>

<style scoped>

</style>