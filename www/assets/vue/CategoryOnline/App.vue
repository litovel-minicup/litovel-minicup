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
            ...mapActions(['subscribe']),
            ...mapMutations(['setBaseLogosPath'])
        },
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.categoryId = Number(data.categoryId);

            this.setBaseLogosPath(data.baseLogosPath);
            this.subscribe(this.categoryId);
        },
    }
</script>

<style scoped>

</style>