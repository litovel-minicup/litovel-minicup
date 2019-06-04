<template>
    <div>

        <img
                v-if="current.thumb"
                :src="(current.thumb || '').replace('thumb', '_original')"
                ref="photo"
        >

        <div ref="results"></div>

        <button class="btn btn-default" @click="next">next</button>
    </div>
</template>

<script>
    import {mapActions, mapMutations, mapState} from 'vuex'
    import * as bodyPix from "@tensorflow-models/body-pix";
    import * as cocoSSD from '@tensorflow-models/coco-ssd';


    const counterHandler = {
        get: function(target, name) {
            return target.hasOwnProperty(name) ? target[name] : 0;
        }
    };

    const COLOR_BIT_SHIFT = 6;

    import _ from 'lodash'

    export default {
        name: "App",
        data() {
            return {
                current: {},
                bodyPixNet: null,
            }
        },
        computed: {
            ...mapState(['photos'])
        },

        methods: {
            ...mapActions(['loadPhotos']),
            ...mapMutations(['setConf']),
            async process() {
                const model = await cocoSSD.load('mobilenet_v2');

                const poses = await model.detect(this.$refs.photo);

                const persons = _.filter(poses, {class: 'person'});

                _.each(persons, async ({bbox}) => {
                    const [x, y, w, h] = bbox;

                    const cropCanvas = document.createElement('canvas');
                    cropCanvas.height = h;
                    cropCanvas.width = w;
                    const cropCtx = cropCanvas.getContext('2d');
                    cropCtx.drawImage(this.$refs.photo, x, y, w, h, 0, 0, w, h);
                    const cropped = cropCtx.getImageData(0, 0, w, h);
                    console.log({x, y, w, h});

                    const resultCanvas = document.createElement('canvas');
                    resultCanvas.width = w;
                    resultCanvas.height = h;
                    this.$refs.results.appendChild(resultCanvas);
                    const resultCtx = resultCanvas.getContext('2d');
                    resultCtx.putImageData(cropped, 0, 0);

                    const resultImg = document.createElement('img');
                    resultImg.src = resultCanvas.toDataURL("image/png");

                    const segmentation = await this.bodyPixNet.estimatePartSegmentation(cropped, 16, 0.35);
                    const total = segmentation.height * segmentation.width;
                    const hist = new Proxy({}, counterHandler);
                    for (let i = 0; i < total; i++) {
                        if (segmentation.data[i] === 12 || segmentation.data[i] === 13) { // is body
                            const [r, g, b, a] = resultCtx.getImageData(
                                i % segmentation.width,
                                (i / segmentation.width) >> 0,
                                1,
                                1
                            ).data;

                            hist[[
                                r >> COLOR_BIT_SHIFT,
                                g >> COLOR_BIT_SHIFT,
                                b >> COLOR_BIT_SHIFT,
                            ]] += 1;
                        }
                    }
                    console.log(hist);
                    console.log(_.sortBy(_.toPairs(hist), [(p) => -p[1]]).slice(0, 10));
                });

            },
            async next() {
                let idx = _.findIndex(this.photos, this.current);
                this.current = this.photos[(idx + 1) % this.photos.length];
                await this.process();
            },
        },
        async mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.setConf(data);

            this.loadPhotos();
        },
        async created() {
            this.bodyPixNet = await bodyPix.load();
        },

        watch: {
            async 'photos'(new_, old) {
                this.current = new_[0];
                await this.process();
            }
        }
    }
</script>

