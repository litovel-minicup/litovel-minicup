<template>
    <div class="fb-video" :data-href="videoLink" data-width="665" data-show-text="false">
        <blockquote :cite="videoLink" class="fb-xfbml-parse-ignore">
            <!-- <a :href="videoLink">mladší - SOKOL BŘVE vs. VELKÉ
                MEZIŘÍČÍ</a>
            <p>
                Přenos ze zápasu v mladší kategorii - SOKOL BŘVE - VELKÉ MEZIŘÍČÍ
                Házená Velké Meziříčí TJ SOKOL Břve - ženy
            </p>
            Zveřejnil(a) <a href="https://www.facebook.com/litovel.minicup/">Litovel Minicup</a> -->
        </blockquote>
    </div>
</template>

<script>
    export default {
        name: "FacebookVideo",
        props: ['facebookVideoId'],
        computed: {
            videoLink() {
                return 'https://www.facebook.com/litovel.minicup/videos/' + this.facebookVideoId + '/';
            }
        }
    }
</script>

<style scoped>
    .fb-video {
        height: 374px;
    }
</style>