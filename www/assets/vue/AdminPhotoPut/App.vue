<template>
    <div>
        <Uploader :uploadUrl="uploadUrl" @uploaded="uploaded" ref="uploader"></Uploader>
        <Tagger :uploadId="uploadId" :photosUrl="photosUrl" ref="tagger"/>
    </div>
</template>

<script>
    import Uploader from './components/Uploader'
    import Tagger from "./components/Tagger";

    export default {
        name: "App",
        components: {Tagger, Uploader},
        data() {
            return {uploadUrl: '', uploadId: '', photosUrl: ''}
        },
        computed: {},

        methods: {
            uploaded() {
                this.$refs.tagger.refresh();
            }
        },
        mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.uploadUrl = data.uploadUrl;
            this.photosUrl = data.photosUrl;
            this.uploadId = data.uploadId;
            this.$nextTick(() => {
                this.$refs.tagger.refresh();
            });
        },
    }
</script>

