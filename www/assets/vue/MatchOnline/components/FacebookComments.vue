<template>
    <div>
        <div class="fb-comments" data-width="665" data-numposts="3"></div>
    </div>
</template>

<script>

    export default {
        name: "FacebookVideo",
        methods: {
            parseFacebook() {
                try {
                    FB.XFBML.parse(this.$el.parent);
                } catch (e) {
                }
            }
        },
        mounted() {
            this.parseFacebook()
        }
    }
</script>

<style scoped>

</style>