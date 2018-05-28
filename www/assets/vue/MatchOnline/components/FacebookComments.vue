<template>
    <div class="fb-comments" :data-href="link" data-width="665" data-numposts="3"></div>
</template>

<script>

    import VueLoading from 'vue-loading-template'

    export default {
        name: "FacebookVideo",
        components: {
            VueLoading,
        },
        props: ['link'],
        methods: {
            parseFacebook() {
                try {
                    FB.XFBML.parse(this.$el.parent);
                } catch (e) {
                }
            }
        },
        watch: {
            link(new_, old) {
                if (new_) this.parseFacebook();
            }
        },
        mounted() {
            this.parseFacebook()
        }
    }
</script>

<style scoped>
    .fb-video {
        height: 374px;
    }
</style>