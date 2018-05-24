<template>
    <div>
        <div v-if="hasOnlineMatches" class="Box__content Live">
            <match-panel
                    v-for="(match, match_id) in onlineMatches"
                    :key="match.id"
                    :match="match"
                    :full="true"
            ></match-panel>
        </div>
        <template v-if="hasUpcomingMatches">
            <div v-if="hasOnlineMatches" class="Article__head Box__head">
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
            },
            hasOnlineMatches() {
                return !_.isEmpty(this.onlineMatches)
            },
            hasUpcomingMatches() {
                return !_.isEmpty(this.upcomingMatches)
            },
        },
        filters: {
            isOnline(match) {
                return _.includes(['half_first', 'pause', 'half_second'], match.state);
            },
        },
        created() {
            this.$store.watch(
                (state) => state.matches,
                (new_, old) => {

                    if (_.size(old) !== _.size(new_) && _.size(_.pickBy(new_, (match, _) => {
                        return _.includes(['half_first', 'pause', 'half_second'], match.state)
                    })) < 4)
                        this.subscribe();
                }
            );
        }
    }
</script>

<style scoped>

</style>