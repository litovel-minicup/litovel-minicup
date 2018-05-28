<template>
    <div>
        <transition name="load">
            <category-online v-if="hasMatches"></category-online>
        </transition>
        <vue-loading
                v-if="!hasMatches"
                type="spin" color="#0e5eff"
                :size="{ width: '100px', height: '100px' }"
        ></vue-loading>
    </div>
</template>

<script>
    import CategoryOnline from "./components/CategoryOnline";
    import VueLoading from 'vue-loading-template'
    import {mapActions, mapMutations, mapState} from 'vuex';
    import _ from 'lodash';

    export default {
        name: "App",
        components: {
            CategoryOnline,
            VueLoading
        },
        data() {
            return {
                categoryId: 0
            }
        },
        computed: {
            category() {
                return this.categoryId;
            },
            ...mapState({
                hasMatches: (state) => !_.isEmpty(state.matches)
            })
        },

        methods: {
            ...mapActions(['subscribe', 'subscribeFallback']),
            ...mapMutations(['setBaseLogosPath', 'setMatchDetailUrlPattern'])
        },
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.categoryId = Number(data.categoryId);

            this.setBaseLogosPath(data.baseLogosPath);
            this.setMatchDetailUrlPattern(data.matchDetailUrlPattern);
            this.subscribe(this.categoryId);

            !this.$store.state.socket.isConnected && this.subscribeFallback();

            this.$store.watch(
                (state) => {
                    return state.socket.isConnected;
                },
                (old, new_) => {
                    // load match after connect
                    !old && new_ && this.subscribe();
                }
            );
            this.$store.watch(
                (state) => {
                    return state.socket.reconnectionCount;
                },
                (old, new_) => {
                    // use load fallback every 60 reconnect fails (include first attempt)
                    !(new_ % 60) && this.subscribeFallback();
                }
            );
        },
    }
</script>

<style scoped>
    .load-enter-active, .load-leave-active {
        transition: opacity .5s;
    }

    .load-enter, .load-leave-to {
        opacity: 0;
    }
</style>