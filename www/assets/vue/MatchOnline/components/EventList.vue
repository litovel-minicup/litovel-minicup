<template>
    <transition-group tag="ul" name="flip-list" class="MatchDetail__action">
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
                :key="0"
        >zápas bez textového přenosu
        </li>

        <!-- State before match. -->
        <li
                class="MatchDetail__action__time"
                v-if="!match.confirmed && !events.length"
                :key="0"
        >před začátkem zápasu v {{ matchStart }}
        </li>
    </transition-group>
</template>

<script>
    import _ from 'lodash'

    export default {
        name: "EventList",
        props: ['events', 'match'],
        computed: {
            eventList() {
                const events = _.sortBy(this.events, ['half_index'], ['time_offset']);
                return this.match.confirmed ? events : events.reverse();
            },
            matchStart() {
                const start = new Date(this.match.match_term_start * 1000);
                // TODO: add date
                return `${start.getHours().pad(2)}:${start.getMinutes().pad(2)}`;/* + (
                    start.getDate() === new Date().getDate() ?
                    '' : ` ${start.getDay()}.${start.getMonth()}.`
                );*/
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