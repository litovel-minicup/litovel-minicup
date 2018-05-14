<template>
    <transition-group tag="ul" name="flip-list" class="MatchDetail__action">
        <template v-for="event in eventList">
            <template v-if="event.type === 'goal' && event.team_index === 0">
                <li class="MatchDetail__action__home" :key="event.id">
                    <div class="MatchDetail__action__home__source">
                        <div>#41 Franta Dzuik</div>
                        <span>{{ event.message }}</span>
                    </div>
                    <span class="MatchDetail__action__home__time">{{ event | eventTime }}</span>
                </li>
            </template>
            <template v-if="event.type === 'goal' && event.team_index === 1">
                <li class="MatchDetail__action__guest" :key="event.id">
                    <span class="MatchDetail__action__guest__time">{{ event | eventTime }}</span>
                    <div class="MatchDetail__action__guest__source">
                        <div>#24 Štěpan Poč</div>
                        <span>{{ event.message }}</span>
                    </div>
                </li>
            </template>
            <template v-if="event.type === 'time'">
                <li class="MatchDetail__action__time" :key="event.message">{{ event.message }}</li>
            </template>
        </template>
    </transition-group>
</template>

<script>
    import _ from 'lodash'

    export default {
        name: "EventList",
        props: ['events', 'match'],
        computed: {
            eventList() {
                let events = [];
                // TODO: eergh, time events are not saved in db, but will be
                if (this.match.state !== 'init') {
                    events.push({type: 'time', message: 'Začátek zápasu'})
                }
                for (let event of _.sortBy(this.events, ['half_index'], ['time_offset'])) {
                    if (events.length && events[events.length - 1].half_index === 0 && event.half_index === 1) {
                        events.push({type: 'time', message: 'Poločas'})
                    }
                    if (event.type === 'goal') {
                        events.push(event);
                    }
                }
                if (this.match.state === 'pause') {
                    events.push({type: 'time', message: 'Poločas'})
                }
                if (this.match.state === 'end') {
                    events.push({type: 'time', message: 'Konec zápasu'})
                }

                return this.match.confirmed ? events : events.reverse();
            }
        },
        filters: {
            eventTime(event) {
                let secs = Number(event.time_offset);
                return `${Math.floor(secs / 60).pad(2)}:${(secs % 60).pad(2)}`;
            }
        }
    }
</script>

<style scoped>
    .flip-list-move {
        transition: transform 1s;
    }
</style>