<template>
    <transition-group tag="ul" name="slideDown" class="MatchDetail__action">
        <template v-for="event in eventList">
            <template v-if="event.type === 'goal' && event.team_index === 0">
                <li class="MatchDetail__action__home" :key="event.id">
                    <div class="MatchDetail__action__home__source">
                        <div v-if="event.player_number">#{{ event.player_number }} {{ event.player_name }}</div>
                        <span>{{ event.message }}</span>
                    </div>
                    <span class="MatchDetail__action__home__time">{{ event | eventTime }}</span>
                </li>
            </template>
            <template v-else-if="event.type === 'goal' && event.team_index === 1">
                <li class="MatchDetail__action__guest" :key="event.id">
                    <span class="MatchDetail__action__guest__time">{{ event | eventTime }}</span>
                    <div class="MatchDetail__action__guest__source">
                        <div v-if="event.player_number">#{{ event.player_number }} {{ event.player_name }}</div>
                        <span>{{ event.message }}</span>
                    </div>
                </li>
            </template>
            <template v-else-if="event.type !== 'goal'">
                <li class="MatchDetail__action__time" :key="event.id">{{ event | eventMessage }}</li>
            </template>
        </template>
        <!-- Matches without live. -->
        <li
                class="MatchDetail__action__time"
                v-if="match.confirmed && !events.length"
                :key="-1"
        >zápas bez textového přenosu
        </li>

        <!-- State before match. -->
        <li
                class="MatchDetail__action__time"
                v-if="!match.confirmed && !events.length"
                :key="-2"
        >před začátkem zápasu v {{ matchStart }}
        </li>

        <li
                :key="-3"
                v-if="events.length > count"
                class="MatchDetail__action__more"
        >
            <div class="Article__more">
                <a @click="count += 10">Zobrazit více</a>
            </div>
        </li>
    </transition-group>
</template>

<script>
    import _ from 'lodash'

    export default {
        name: "EventList",
        props: ['events', 'match'],
        data() {
            return {
                count: 10
            }
        },
        computed: {
            eventList() {
                const events = _.sortBy(this.events, ['half_index'], ['time_offset']);
                return _.slice(this.match.confirmed ? events : events.reverse(), 0, this.count);
            },
            matchStart() {
                const start = new Date(this.match.match_term_start * 1000);

                return `${start.getHours().pad(2)}:${start.getMinutes().pad(2)}` + (
                    start.toLocaleDateString() === new Date().toLocaleDateString() ?
                        '' : ` ${start.toLocaleDateString()}.`
                );
            }
        },
        filters: {
            eventTime(event) {
                let secs = Number(event.time_offset);
                return `${Math.floor(secs / 60).pad(2)}:${(secs % 60).pad(2)}`;
            },
            eventMessage(event) {
                if (event.message) return event.message;
                const msg = (
                    event.type === 'start' && event.half_index === 0
                ) || (
                    event.type === 'end' && event.half_index === 1
                ) ? 'zápasu' : `${event.half_index + 1}. poločasu`;

                return `${{end: 'konec', start: 'začátek'}[event.type]} ${msg}`;
            },
        }
    }
</script>

<style scoped>
    .flip-list-move {
        transition: transform 1s;
    }
</style>