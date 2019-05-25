<template>
    <div>

        <img :src="photo.thumb.replace('thumb', '_original')" alt="" v-for="photo in photos" ref="photo">


        <canvas id="canvas"></canvas>
    </div>
</template>

<script>
    import {mapActions, mapMutations, mapState} from 'vuex'
    import * as bodyPix from "@tensorflow-models/body-pix";

    export default {
        name: "App",
        data() {
            return {}
        },
        computed: {
            ...mapState(['photos'])
        },

        methods: {
            ...mapActions(['loadImages']),
            ...mapMutations(['setConf']),
            async process() {
                const net = await bodyPix.load();

                const segmentation = await net.estimatePartSegmentation(this.$refs.photo[1], 32, 0.5);

                const warm = [
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [84, 101, 214], [75, 113, 221], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                    [0, 0, 0], [0, 0, 0], [0, 0, 0], [0, 0, 0],
                ];


                const invert = true;

// the colored part image is an rgb image with a corresponding color from thee rainbow colors for each part at each pixel, and black pixels where there is no part.
                const coloredPartImage = bodyPix.toColoredPartImageData(segmentation, warm);
                const opacity = 0.7;
                const flipHorizontal = false;
                const maskBlurAmount = 0;
                const canvas = document.getElementById('canvas');
// draw the colored part image on top of the original image onto a canvas.  The colored part image will be drawn semi-transparent, with an opacity of 0.7, allowing for the original image to be visible under.
                bodyPix.drawMask(
                    canvas, this.$refs.photo[1], coloredPartImage, opacity, maskBlurAmount,
                    flipHorizontal);
            }
        },
        async mounted() {
            const data = this.$root.$el.parentElement.dataset;
            this.setConf(data);

            this.loadImages();
        },

        watch: {
            async 'photos'(new_, old) {
                console.profile();
                await this.process();
                console.profileEnd();

            }
        }
    }
</script>

