<template>
    <div>
        <live-bar></live-bar>
    </div>
</template>

<script>
    import LiveBar from "./components/LiveBar";
    import VueLoading from 'vue-loading-template'
    import {mapActions, mapMutations, mapState} from 'vuex';
    import _ from 'lodash';

    export default {
        name: "App",
        components: {
            LiveBar,
            VueLoading
        },
        data() {
            return {}
        },
        computed: {
            ...mapState(['news', 'events'])
        },

        methods: {
            ...mapActions(['subscribe', 'loadNews']),
            ...mapMutations(['setMatchDetailUrlPattern'])
        },
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.categoryId = Number(data.categoryId);

            this.setMatchDetailUrlPattern(data.matchDetailUrlPattern);
            this.subscribe(this.categoryId);
            this.loadNews();

            // !this.$store.state.socket.isConnected && this.subscribeFallback();

            this.$store.watch(
                (state) => {
                    return state.socket.isConnected;
                },
                (old, new_) => {
                    // load match after connect
                    !old && new_ && this.subscribe();
                }
            );
            /* this.$store.watch(
                (state) => {
                    return state.socket.reconnectionCount;
                },
                (old, new_) => {
                    // use load fallback every 60 reconnect fails (include first attempt)
                    !(new_ % 60) && this.subscribeFallback();
                }
            ); */
        },
    }
</script>

<style scoped>
    .load-enter-active, .load-leave-active {
        transition: all .5s;
    }

    .load-enter, .load-leave-to {
        transform-origin: top center;
        transform: scaleY(0);
    }
</style>