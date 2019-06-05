<template>
    <div>
        <label>autonext <input type="checkbox" v-model="autonext"></label>
        <div>
            <span v-for="p in photos">
                <img
                        :src="(p.thumb || '')"
                        @click="current=p; process()" alt=""
                        width="40"
                ></span>
        </div>

        <div v-for="person in persons" class="col-md-3" style="margin: 2em;">
            <img :src="person.imgSrc" ref="resultImg">
            <select v-model="person.id" class="form-control">
                <option :value="null">žádný</option>
                <option :value="team.id" v-for="team in teams" v-text="team.name"></option>
            </select>
            <button class="btn btn-default"  @click="match(m)" v-for="m in person.matches">{{ m[0].name }} ({{ m[1].length }}), </button>
            <div class="btn-group">
                <a type="button"  class="btn btn-primary form-control" @click="save(person)">uložit hist</a>
                <a type="button"  class="btn btn-warning form-control" @click="skip(person)">z toho nic nebude</a>
            </div>
        </div>

        <button class="btn btn-warning pull-right" @click="next">next</button>
        <img
                v-if="current.thumb"
                :src="(current.thumb || '').replace('thumb', '_original')"
                ref="photo"
        >

        <div ref="results"></div>
    </div>
</template>

<script>
    import {mapActions, mapMutations, mapState} from 'vuex'
    import * as bodyPix from "@tensorflow-models/body-pix";
    import * as cocoSSD from '@tensorflow-models/coco-ssd';


    const counterHandler = {
        get: function (target, name) {
            return target.hasOwnProperty(name) ? target[name] : 0;
        }
    };

    const COLOR_BIT_SHIFT = 5;

    import _ from 'lodash'

    export default {
        name: "App",
        data() {
            return {
                current: {},
                bodyPixNet: null,
                persons: [],
                autonext: false,
            }
        },
        computed: {
            ...mapState(['photos', 'teams'])
        },

        methods: {
            ...mapActions(['loadPhotos', 'loadTeams', 'uploadTeams', 'findBestMatches', 'addTeamTag']),
            ...mapMutations(['setConf', 'setHistogram']),

            async drawBodyPixSegmentation(segmentation, resultCanvas, resultImg) {
                const warm = [
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 255], [0, 0, 255], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                ];

                const coloredPartImage = bodyPix.toColoredPartImageData(segmentation, warm);
                const opacity = 0.7;
                const flipHorizontal = false;
                const maskBlurAmount = 1;
                bodyPix.drawMask(
                    resultCanvas,
                    resultImg,
                    coloredPartImage,
                    opacity,
                    maskBlurAmount,
                    flipHorizontal
                );
            },
            async process() {
                const model = await cocoSSD.load('mobilenet_v2');

                const poses = await model.detect(this.$refs.photo);

                const persons = _.filter(_.filter(poses, {class: 'person'}), ({score}) => score > .75);
                this.persons = [];
                _.each(persons, async ({bbox}, idx) => {
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
                    // this.$refs.results.appendChild(resultCanvas);
                    const resultCtx = resultCanvas.getContext('2d');
                    resultCtx.putImageData(cropped, 0, 0);

                    const resultImg = document.createElement('img');
                    resultImg.src = resultCanvas.toDataURL("image/png");

                    const segmentation = await this.bodyPixNet.estimatePartSegmentation(cropped, 16, 0.35);
                    // this.$refs.results.appendChild(resultCanvas);
                    await this.drawBodyPixSegmentation(segmentation, resultCanvas, resultImg);
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
                    const histogramTop = _.map(
                        _.sortBy(
                            _.toPairs(hist),
                            [(p) => -p[1]]
                        ).slice(0, 5),
                        (s) => _.map(s[0].split(','), (s) => Number(s))
                    );

                    const bestMatches = await this.findBestMatches({histogram: histogramTop});

                    this.persons.push({
                        histogram: histogramTop,
                        imgSrc: resultImg.src,
                        id: null,
                        matches: bestMatches,
                    });
                });

            },
            async next() {
                let idx = _.findIndex(this.photos, this.current);
                this.current = this.photos[(idx + 1) % this.photos.length];

                this.persons = [];
                setTimeout(async () => {
                    await this.process();
                }, 1000);
            },
            async save(person) {
                this.setHistogram({id: person.id, histogram: person.histogram});
                this.uploadTeams();
                this.persons = _.reject(this.persons, person);
            },
            async skip(person) {
                this.persons = _.reject(this.persons, person);
            },
            async match(match) {
                this.addTeamTag({
                    team: match[0].id,
                    photo: this.current.id,
                });
                this.skip(match);
            }
        },
        async mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.setConf(data);

            this.loadPhotos();
            this.loadTeams();
        },
        async created() {
            this.bodyPixNet = await bodyPix.load();
        },
        watch: {
            'persons'(new_, old) {
                _.each(new_, (p) => {
                    const fullMatch = _.filter(p.matches, (m) => m[1].length === 5).length === 1;
                    const partMatch = _.filter(p.matches, (m) => m[1].length === 4).length === 1;

                    if (fullMatch || (!fullMatch && partMatch)) {
                        this.addTeamTag({
                            team: p.matches[0][0].id,
                            photo: this.current.id,
                        });
                        this.skip(p);
                    }
                });
                new_.length && this.autonext && this.next();
            }
        }
    }
</script>

