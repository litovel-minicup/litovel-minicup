<template>
    <form :action="uploadUrl" ref="form" enctype="multipart/form-data" method="POST" @submit.prevent="upload">
        <progress max="100" :value.prop="uploadPercentage"></progress>
        {{ current }} / {{ total }}

        <input type="file" ref="files" multiple name="images">
        <input type="submit" value="send">
    </form>
</template>

<script>
    import _ from 'lodash'
    import UploadImage from 'vue-upload-image';
    import axios from 'axios'

    export default {
        components: {
            UploadImage,
        },

        data() {
            return {uploadPercentage: 0, current: -1, total: null}
        },
        props: {
            uploadUrl: String,
        },
        methods: {
            upload() {
                /**
                 * @type HTMLFormElement
                 */
                const form = this.$refs.form;
                let promises = [];
                this.total = this.$refs.files.files.length;
                for (var i = 0; i < this.$refs.files.files.length; i++) {
                    let file = this.$refs.files.files[i];
                    const data = new FormData();
                    this.current = i;

                    data.append('images[0]', file);
                    promises.push(axios.post(this.uploadUrl, data, {
                        headers: {
                            'content-type': 'multipart/form-data'
                        },
                        onUploadProgress: function (progressEvent) {
                            this.uploadPercentage = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
                        }.bind(this)
                    }))
                }
                Promise.all(promises).then(() => {
                    form.reset();
                    this.$emit('uploaded');
                })
            }
        }
    }
</script>