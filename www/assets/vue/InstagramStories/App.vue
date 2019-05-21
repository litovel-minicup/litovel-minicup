<template>
    <div>
        <div class="Aside" v-if="stories.length">
            <div class="Box__head Aside__head">
                <div class="Aside__head__text Box__head__text">
                    Aktuální dění
                </div>
            </div>
            <div class="Box__content Aside__content">
                <div class="Stories">
                    <div class="Stories__story" v-for="(story, i) in stories" @click="selected = story">
                        <template v-if="story.type === 'video'">
                            <video :src="story.url + '#0.1'" preload="metadata"></video>
                        </template>
                        <template v-if="story.type === 'image'">
                            <img :src="story.url" alt="">
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="Stories__backdrop" v-if="selected" @click="selected = null">
            <div class="Stories__detail" >
                <template v-if="selected.type === 'video'">
                    <video :src="selected.url + '#0.1'" preload="metadata" controls autoplay @click.stop></video>
                </template>
                <template v-if="selected.type === 'image'">
                    <img :src="selected.url" alt="Aktuální dění z turnaje." @click.stop>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapState, mapActions} from 'vuex'

    export default {
        name: "App",
        data() {
            return {selected: null}
        },
        computed: {
            ...mapState(['stories']),
        },
        methods: {
            ...mapActions(['loadStories']),
        },
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.loadStories(data.username);
        }
    }
</script>

<style scoped lang="scss">
    .load-enter-active, .load-leave-active {
        transition: opacity .5s;
    }

    .load-enter, .load-leave-to {
        opacity: 0;
    }

    .Stories {
        overflow-x: scroll;
        white-space: nowrap;


        &__story {
            display: inline-block;
            max-width: 25%;
            width: 25%;
            padding: 8px 2px;
            flex: auto;

            VIDEO, IMG {
                max-width: 100%;
            }
        }

        &__backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #000000cc;
            z-index: 1500;
            text-align: center;
            display: flex;
        }

        &__detail {
            flex: auto;
            margin: auto;
            display: inline-block;
            VIDEO, IMG {
                max-height: 100vh;
            }
        }
    }
</style>