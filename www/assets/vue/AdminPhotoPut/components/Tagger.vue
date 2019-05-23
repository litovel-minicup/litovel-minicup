<template>
    <div>
        <hr>
        <div class="row text-center">


            <div class="btn-group">
                <button
                        v-for="tag in mainTags"
                        v-text="tag.name"
                        @click="toggleTag(tag.id)"
                        class="btn btn-lg"
                        :class="selectedTags.includes(tag.id) ? 'btn-primary' : 'btn-default'"
                ></button>
            </div>

            <button class="btn btn-lg btn-success" @click="updatePhotosTags">Otaguj!</button>
            <button class="btn btn-lg btn-primary"
                    @click="insertPhotos(selectedPhotos.length ? selectedPhotos : photos.map((p) => p.id)).then(() => {selectedPhotos = []})">Vlož!</button>
            <button class="btn btn-lg btn-danger"
                    @click="deletePhotos(selectedPhotos.length ? selectedPhotos : photos.map((p) => p.id)).then(() => {selectedPhotos = []})">Smaž!</button>
            <button class="btn btn-lg btn-warning">Smaž tagy!</button>
            <button class="btn btn-default btn-lg" v-if="selectedPhotos.length">Vybráno {{ selectedPhotos.length }}
            </button>
        </div>
        <hr>
        <div class="row">
            <span
                    v-for="photo in photos"
                    class="photo"
                    :class="{selected: selectedPhotos.includes(photo.id)}"
                    @click="togglePhoto(photo.id)"
            >
                <img :src="photo.thumb.replace('thumb', '_original')" alt="" class="img-responsive">
                <span class="tags-count" v-text="photo.tags"></span>
            </span>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import axios from 'axios'
    import {mapState, mapActions} from 'vuex'
    import _ from 'lodash'

    export default {
        name: 'Tagger',
        props: {
            uploadId: {},
        },
        data() {
            return {
                selectedTags: [],
                selectedPhotos: [],
            }
        },
        computed: {
            ...mapState(['mainTags', 'photos']),
        },
        mounted() {
            this.refresh();
        },
        methods: {
            ...mapActions(['updateTags', 'refreshPhotos', 'insertPhotos', 'deletePhotos']),
            refresh() {
                this.refreshPhotos();
            },
            toggleTag(id) {
                if (this.selectedTags.includes(id))
                    Vue.delete(this.selectedTags, this.selectedTags.indexOf(id));
                else
                    this.selectedTags.push(id);

            },
            togglePhoto(id) {
                if (this.selectedPhotos.includes(id))
                    Vue.delete(this.selectedPhotos, this.selectedPhotos.indexOf(id));
                else
                    this.selectedPhotos.push(id);

            },
            updatePhotosTags() {
                this.updateTags({
                    tags: this.selectedTags,
                    photos: this.selectedPhotos.length ? this.selectedPhotos : _.map(this.photos, (p) => p.id),
                })
            }
        }
    }
</script>

<style scoped lang="scss">
    img {
        max-height: 12vw;
    }

    .photo {
        display: inline-block;
        margin: 5px;
        position: relative;

        &:hover {
            opacity: .8;
        }

        .tags-count {
            position: absolute;
            right: 0;
            bottom: 0;
            background-color: black;
            color: white;
            font-size: 32pt;
            line-height: 1;
            padding: 2px 4px;
        }

        &.selected {
            &:after {
                content: attr(data-tags);
                position: absolute;
                background-color: #CD1818;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                opacity: .5;
            }
        }
    }
</style>
