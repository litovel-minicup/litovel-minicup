<template>
    <div>
        <hr>
        <div class="row">
            <div class="btn-group btn-group-tags">
                <button
                        v-for="tag in mainTags"
                        v-text="tag.name"
                        @click="toggleTag(tag.id)"
                        class="btn"
                        :class="selectedTags.includes(tag.id) ? 'btn-primary' : 'btn-default'"
                ></button>
                <select class="form-control another-tag-select pull-left"
                        v-model="anotherTag"
                        @change="selectedAnotherTag()"
                >
                    <option :value="0">...další tagy...</option>
                    <option
                            :value="opt.id"
                            v-text="opt.name"
                            v-for="opt in availableAnotherTags"
                    ></option>
                </select>

                <br>
                <button
                        @click="setSelectedTags([]); setAnotherTags([]);"
                        class="btn btn-warning"
                        v-if="selectedTags.length || anotherTags.length"
                >&cross;
                </button>
                <button
                        v-for="tag in anotherTags"
                        v-text="tag.name"
                        @click="removeAnotherTag(tag.id)"
                        class="btn btn-primary"
                ></button>

            </div>

            <div class="btn-group pull-right">
                <button class="btn btn-default btn-lg" v-if="selectedPhotos.length" @click="setSelectedPhotos([])">
                    Unselect {{ selectedPhotos.length }}
                </button>

                <button class="btn btn-lg btn-success" @click="updateTags">Otaguj!</button>
                <button class="btn btn-lg btn-primary" @click="insertPhotos()">Vlož!</button>
                <button class="btn btn-lg btn-danger" @click="deletePhotos()">Smaž!</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <span
                    v-for="photo in photos"
                    class="photo"
                    :class="{selected: selectedPhotos.includes(photo.id)}"
                    @click.exact="togglePhoto(photo.id)"
                    @click.shift.exact="selectMultiplePhotos(photo.id)"
            >
                <img :src="photo.thumb" alt="" class="img-responsive">
                <span class="tags-count">{{ photoLabel(photo.tags) }}</span>
                <span class="time">{{ photo.taken | takenString }}</span>
            </span>
        </div>
    </div>
</template>
<script>
    import Vue from 'vue'
    import axios from 'axios'
    import {mapState, mapActions, mapMutations, mapGetters} from 'vuex'
    import _ from 'lodash'

    export default {
        name: 'Tagger',
        props: {
            uploadId: {},
        },
        data() {
            return {anotherTag: 0}
        },
        computed: {
            ...mapState(['tags', 'photos', 'selectedTags', 'selectedPhotos', 'anotherTags']),
            mainTags() {
                return _.filter(this.tags, _.property('main'));
            },
            noMainTags() {
                return _.orderBy(_.reject(this.tags, _.property('main')).reverse(), _.property('name'));
            },
            availableAnotherTags() {
                let selectedAnother = _.map(this.anotherTags, _.property('id'));
                return _.reject(this.noMainTags, (t) => {
                    return selectedAnother.includes(t.id);
                })
            },
            photoLabel() {
                return (tags) => {
                    return tags.map((id) => (_.find(this.tags, {id}) || {name: '???'}).name).join(',');
                }
            }
        },
        mounted() {
            this.refresh();
        },
        filters: {
            takenString(tmp) {
                return (new Date(tmp * 1000).toLocaleString());
            }
        },
        methods: {
            ...mapActions([
                'refreshPhotos', 'insertPhotos', 'deletePhotos',
                'toggleTag', 'togglePhoto', 'selectMultiplePhotos'
            ]),
            ...mapMutations(['updateTags', 'setSelectedPhotos', 'setSelectedTags', 'setAnotherTags']),
            selectedAnotherTag() {
                let tag = _.find(this.tags, {id: this.anotherTag});
                tag && this.setAnotherTags([...this.anotherTags, tag]);
            },
            removeAnotherTag(id) {
                this.setAnotherTags(_.reject(this.anotherTags, {id}));
            },
            refresh() {
                this.refreshPhotos();
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
        cursor: pointer;

        &:hover {
            opacity: .8;
        }

        .tags-count {
            position: absolute;
            right: 0;
            bottom: 0;
            background-color: black;
            color: white;
            font-size: 8pt;
            line-height: 1;
            padding: 2px 4px;
        }

        .time {
            position: absolute;
            right: 0;
            top: 0;
            background-color: black;
            color: white;
            font-size: 8pt;
            line-height: 1;
            padding: 2px 4px;
        }

        &.selected {
            &:after {
                content: "";
                position: absolute;
                background-color: white;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                opacity: .6;
            }
        }
    }

    .another-tag-select {
        display: inline-block;
        width: auto;
    }

    .btn-group-tags {
        max-width: 75%;
    }
</style>
