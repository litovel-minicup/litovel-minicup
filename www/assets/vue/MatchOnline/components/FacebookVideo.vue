<template>
    <div class="fb-video" :data-href="facebookVideoUrl" data-width="665" data-show-text="false">
        <blockquote :cite="facebookVideoUrl" class="fb-xfbml-parse-ignore">
            <!-- <a :href="videoLink">mladší - SOKOL BŘVE vs. VELKÉ
                MEZIŘÍČÍ</a>
            <p>
                Přenos ze zápasu v mladší kategorii - SOKOL BŘVE - VELKÉ MEZIŘÍČÍ
                Házená Velké Meziříčí TJ SOKOL Břve - ženy
            </p>
            Zveřejnil(a) <a href="https://www.facebook.com/litovel.minicup/">Litovel Minicup</a> -->
            <vue-loading
                    type="spin" color="#0e5eff"
                    :size="{ width: '100px', height: '100px' }"
            ></vue-loading>
        </blockquote>
    </div>
</template>

<script>

    import VueLoading from 'vue-loading-template'

    export default {
        name: "FacebookVideo",
        components: {
            VueLoading,
        },
        props: ['facebookVideoUrl'],
        methods: {
            parseFacebook() {
                try {
                    FB.XFBML.parse(this.$el.parent);
                } catch (e) {
                }
            }
        },
        watch: {
            facebookVideoId(new_, old) {
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