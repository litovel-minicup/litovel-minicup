<template>
    <div>
        <category-online></category-online>
    </div>
</template>

<script>
    import CategoryOnline from "./components/CategoryOnline";
    import {mapActions, mapMutations} from 'vuex';

    export default {
        name: "App",
        components: {
            CategoryOnline
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

</style>