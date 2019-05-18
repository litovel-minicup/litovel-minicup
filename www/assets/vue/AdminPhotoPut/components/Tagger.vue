<template>
    <div class="row">
        <div class="panel col-xs-3" v-for="photo in photos">
            <div class="panel-body text-center">
                <img :src="photo.thumb.replace('thumb', '_original')" alt="" class="img-responsive">
            </div>

        </div>
    </div>
</template>
<script>
    import axios from 'axios'

    export default {
        name: 'Tagger',
        props: {
            uploadId: {},
            photosUrl: {},
        },
        data() {
            return {
                photos: [],
            }
        },
        mounted() {
            this.refresh();
        },
        methods: {
            refresh() {
                axios.get(this.photosUrl).then((r) => {
                    this.photos = r.data.photos;
                })
            }
        }
    }
</script>

<style scoped>
    img {
        max-height: 100px;
    }
</style>
