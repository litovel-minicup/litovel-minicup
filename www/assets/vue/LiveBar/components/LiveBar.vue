<template>
    <div>
        <div class="LiveBar">
            <div class="LiveBar__content">
                <transition name="fade">
                <span class="LiveBar__match" :key="key">
                    <span v-if="displayed.match_id">
                        <strong>
                            {{ matches[displayed.match_id].home_team_name }}
                        </strong>
                            vs.
                        <strong>
                            {{ matches[displayed.match_id].away_team_name }}

                            {{ matches[displayed.match_id].score[0] }}:{{ matches[displayed.match_id].score[1] }} —
                        </strong>
                    </span>
                    <span v-html="messageToDisplay"></span>
                </span>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import _ from 'lodash';

    export default {
        name: "LiveBar",
        methods: {
            ...mapActions(['subscribe']),
            replace() {
                let msg = this.news[this.key = Math.floor(Math.random() * this.news.length)];
                this.displayed = {message: msg};
                this.timeoutID = setTimeout(this.replace, 8000);
            }
        },
        computed: {
            ...mapState(['matches', 'events', 'news']),
            messageToDisplay() {
                return this.$options.filters.eventMessage(this.displayed);
            }
        },
        data() {
            return {timeoutID: 0, displayed: null, key: 0}
        },
        filters: {
            isOnline(match) {
                return _.includes(['half_first', 'pause', 'half_second'], match.state);
            },
            eventMessage(event) {
                if (event.message) return event.message;
                if (!event.type) return '';
                const msg = (
                    event.type === 'start' && event.half_index === 0
                ) || (
                    event.type === 'end' && event.half_index === 1
                ) ? 'zápasu' : `${event.half_index + 1}. poločasu`;

                return `${{end: 'Konec', start: 'Začátek'}[event.type]} ${msg}.`;
            },
        },
        mounted() {
            this.replace();
        },
        watch: {
            'events'(new_) {
                this.displayed = new_[new_.length - 1];
                this.key = this.displayed.id;
                clearTimeout(this.timeoutID);
                this.timeoutID = setTimeout(this.replace, 8000);
            },
            'news'() {
                clearTimeout(this.timeoutID);
                this.replace();
            }
        },
        destroyed() {
            clearTimeout(this.timeoutID);
        }
    }
</script>

<style scoped>
    .fade-enter-active, .fade-leave-active {
        transition: all 1s;
    }

    .fade-enter, .fade-leave-active {
        transform: translateY(-100%);
    }

    .fade-enter {
        transform: translateY(0);
    }

    .fade-leave-active {
        transform: translateY(100%);
    }
</style>