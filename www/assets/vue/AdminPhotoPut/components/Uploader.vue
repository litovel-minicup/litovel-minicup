<template>
    <div class="example-full">

        <div class="pull-right">
            <button type="button" class="btn btn-danger float-right btn-is-option"
                    @click.prevent="isOption = !isOption">
                <i class="fa fa-cog" aria-hidden="true"></i>
                Options
            </button>
            <file-upload
                    class="btn btn-primary"
                    :put-action="uploadUrl"
                    :extensions="extensions"
                    :accept="accept"
                    :multiple="multiple"
                    :directory="directory"
                    :size="size || 0"
                    :thread="thread < 1 ? 1 : (thread > 5 ? 5 : thread)"
                    :headers="headers"
                    :data="data"
                    :drop="drop"
                    :drop-directory="dropDirectory"
                    :add-index="addIndex"
                    v-model="files"
                    @input-filter="inputFilter"
                    @input-file="inputFile"
                    ref="upload">
            </file-upload>
        </div>

        <div v-show="$refs.upload && $refs.upload.dropActive" class="drop-active">
            <h3>Drop files to upload</h3>
        </div>
        <div class="upload" v-show="!isOption">
            <table class="table table-hover">
                <tbody>
                <tr v-if="!filteredFiles.length">
                    <td colspan="7">
                        <div class="text-center p-5">
                            <label :for="name" class="btn btn-lg btn-warning">Vyber soubory</label>
                        </div>
                    </td>
                </tr>
                <tr v-for="(file, index) in filteredFiles" :key="file.id">
                    <td>{{index}}</td>
                    <td>
                        <img v-if="file.thumb" :src="file.thumb" width="75" height="auto"/>
                        <span v-else>No Image</span>
                    </td>
                    <td>
                        <!-- <div class="filename d-none">
                            {{file.name}}
                        </div> -->
                        <div class="progress" v-if="file.active || file.progress !== '0.00'">
                            <div :class="{'progress-bar': true, 'progress-bar-striped': true, 'bg-danger': file.error, 'progress-bar-animated': file.active}"
                                 role="progressbar" :style="{width: file.progress + '%'}">{{file.progress}}%
                            </div>
                        </div>
                    </td>
                    <td>{{file.size | formatSize}}</td>
                    <td></td>

                    <td v-if="file.error">{{file.error}}</td>
                    <td v-else-if="file.success">success</td>
                    <td v-else-if="file.active">active</td>
                    <td v-else></td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-sm btn-warning" :class="{disabled: !file.active}" href="#"
                               @click.prevent="file.active ? $refs.upload.update(file, {error: 'cancel'}) : false">Cancel</a>

                            <a class="btn btn-sm btn-info" href="#" v-if="file.active"
                               @click.prevent="$refs.upload.update(file, {active: false})">Abort</a>

                            <a class="btn btn-sm btn-info" href="#"
                               v-else-if="file.error && file.error !== 'compressing' && $refs.upload.features.html5"
                               @click.prevent="$refs.upload.update(file, {active: true, error: '', progress: '0.00'})">Retry
                                upload</a>

                            <a class="btn btn-sm btn-success"
                               :class="{disabled: file.success || file.error === 'compressing'}"
                               href="#" v-else
                               @click.prevent="file.success || file.error === 'compressing' ? false : $refs.upload.update(file, {active: true})">Upload</a>

                            <a class="btn btn-sm btn-danger" href="#"
                               @click.prevent="$refs.upload.remove(file)">Remove</a>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <div class="option" v-show="isOption">
            <div class="form-group">
                <label for="accept">Accept:</label>
                <input type="text" id="accept" class="form-control" v-model="accept">
                <small class="form-text text-muted">Allow upload mime type</small>
            </div>
            <div class="form-group">
                <label for="extensions">Extensions:</label>
                <input type="text" id="extensions" class="form-control" v-model="extensions">
                <small class="form-text text-muted">Allow upload file extension</small>
            </div>
            <div class="form-group">
                <label for="thread">Thread:</label>
                <input type="number" max="5" min="1" id="thread" class="form-control" v-model.number="thread">
                <small class="form-text text-muted">Also upload the number of files at the same time (number of
                    threads)
                </small>
            </div>
            <div class="form-group">
                <label for="size">Max size:</label>
                <input type="number" min="0" id="size" class="form-control" v-model.number="size">
            </div>
            <div class="form-group">
                <label for="minSize">Min size:</label>
                <input type="number" min="0" id="minSize" class="form-control" v-model.number="minSize">
            </div>
            <div class="form-group">
                <label for="autoCompress">Automatically compress:</label>
                <input type="number" min="0" id="autoCompress" class="form-control" v-model.number="autoCompress">
                <small class="form-text text-muted" v-if="autoCompress > 0">More than {{autoCompress | formatSize}}
                    files are automatically compressed
                </small>
                <small class="form-text text-muted" v-else>Set up automatic compression</small>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" id="add-index" class="form-check-input" v-model="addIndex"> Start
                        position to add
                    </label>
                </div>
                <small class="form-text text-muted">Add a file list to start the location to add</small>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" id="drop" class="form-check-input" v-model="drop"> Drop
                    </label>
                </div>
                <small class="form-text text-muted">Drag and drop upload</small>
            </div>
            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" id="drop-directory" class="form-check-input" v-model="dropDirectory">
                        Drop directory
                    </label>
                </div>
                <small class="form-text text-muted">Not checked, filter the dragged folder</small>
            </div>
            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" id="upload-auto" class="form-check-input" v-model="uploadAuto"> Auto
                        start
                    </label>
                </div>
                <small class="form-text text-muted">Automatically activate upload</small>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary btn-lg btn-block" @click.prevent="isOption = !isOption">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</template>
<style>
    .example-full .btn-group .dropdown-menu {
        display: block;
        visibility: hidden;
        transition: all .2s
    }

    .example-full .btn-group:hover > .dropdown-menu {
        visibility: visible;
    }

    .example-full label.dropdown-item {
        margin-bottom: 0;
    }

    .example-full .btn-group .dropdown-toggle {
        margin-right: .6rem
    }


    .example-full .filename {
        margin-bottom: .3rem
    }

    .example-full .btn-is-option {
        margin-top: 0.25rem;
    }

    .example-full .drop-active {
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        position: fixed;
        z-index: 9999;
        opacity: .6;
        text-align: center;
        background: #000;
    }

    .example-full .drop-active h3 {
        margin: -.5em 0 0;
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        font-size: 40px;
        color: #fff;
        padding: 0;
    }
</style>

<script>
    import ImageCompressor from '@xkeshi/image-compressor'
    import FileUpload from 'vue-upload-component'
    import _ from 'lodash'

    export default {
        components: {
            FileUpload,
        },

        data() {
            return {
                files: [],
                accept: 'image/png,image/jpeg',
                extensions: 'jpg,jpeg,png',
                // extensions: ['gif', 'jpg', 'jpeg','png', 'webp'],
                // extensions: /\.(gif|jpe?g|png|webp)$/i,
                minSize: 1024,
                size: 1024 * 1024 * 10,
                multiple: true,
                directory: false,
                drop: true,
                dropDirectory: true,
                addIndex: false,
                thread: 3,
                name: 'file',
                headers: {
                    'X-Csrf-Token': 'xxxx',
                },
                data: {
                    '_csrf_token': 'xxxxxx',
                },

                autoCompress: 1024 * 1024,
                uploadAuto: false,
                isOption: false,
                imageCompressor: new ImageCompressor(null, {
                    convertSize: Infinity,
                    height: 1200,
                    quality: .95,
                })
            }
        },
        props: {
            uploadUrl: String,
        },
        computed: {
            filteredFiles() {
                return _.reject(this.files, {'success': true})
            }
        },

        watch: {
            '$refs.upload.uploaded'() {
                this.$emit('uploaded');
            }
        },

        methods: {
            inputFilter(newFile, oldFile, prevent) {
                if (newFile && !oldFile) {
                    // Before adding a file

                    // Filter system files or hide files
                    if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
                        return prevent()
                    }

                    // Filter php html js file
                    if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
                        return prevent()
                    }

                    /*// Automatic compression
                    if (newFile.file && newFile.type.substr(0, 6) === 'image/' && this.autoCompress > 0 && this.autoCompress < newFile.size) {
                        newFile.error = 'compressing';

                        this.imageCompressor.compress(newFile.file)
                            .then((file) => {
                                this.$refs.upload.update(newFile, {error: '', file, size: file.size, type: file.type})
                            })
                            .catch((err) => {
                                this.$refs.upload.update(newFile, {error: err.message || 'compress'})
                            })
                    }*/
                }


                if (newFile && (!oldFile || newFile.file !== oldFile.file)) {

                    // Create a blob field
                    newFile.blob = '';
                    let URL = window.URL || window.webkitURL;
                    if (URL && URL.createObjectURL) {
                        newFile.blob = URL.createObjectURL(newFile.file)
                    }

                    // Thumbnails
                    newFile.thumb = '';
                    if (newFile.blob && newFile.type.substr(0, 6) === 'image/') {
                        newFile.thumb = newFile.blob
                    }
                }
            },

            // add, update, remove File Event
            inputFile(newFile, oldFile) {
                if (newFile && oldFile) {
                    // update

                    if (newFile.active && !oldFile.active) {
                        // beforeSend

                        // min size
                        if (newFile.size >= 0 && this.minSize > 0 && newFile.size < this.minSize) {
                            this.$refs.upload.update(newFile, {error: 'size'})
                        }
                    }

                    if (newFile.progress !== oldFile.progress) {
                        // progress
                    }

                    if (newFile.error && !oldFile.error) {
                        // error
                    }

                    if (newFile.success && !oldFile.success) {
                        this.$emit('uploaded', newFile);
                    }
                }


                if (!newFile && oldFile) {
                    // remove
                    if (oldFile.success && oldFile.response.id) {
                        // $.ajax({
                        //   type: 'DELETE',
                        //   url: '/upload/delete?id=' + oldFile.response.id,
                        // })
                    }
                }


                // Automatically activate upload
                // if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
                //     if (this.uploadAuto && !this.$refs.upload.active) {
                this.$refs.upload.active = true
                //     }
                // }
            },


            alert(message) {
                alert(message)
            },

            // add folader
            onAddFolader() {
                if (!this.$refs.upload.features.directory) {
                    this.alert('Your browser does not support')
                    return
                }

                let input = this.$refs.upload.$el.querySelector('input')
                input.directory = true
                input.webkitdirectory = true
                this.directory = true

                input.onclick = null
                input.click()
                input.onclick = (e) => {
                    this.directory = false
                    input.directory = false
                    input.webkitdirectory = false
                }
            },
        }
    }
</script>