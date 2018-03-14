Number.prototype.pad = function (size) {
    var sign = Math.sign(this) === -1 ? '-' : '';
    return sign + new Array(size).concat([Math.abs(this)]).join('0').slice(-size);
};

function initialize(matchId) {
    var app = new Vue({
        el: '#vue-app',
        data: {
            score: [null, null],
            events: [],
            time: null,
            halfStart: null,
            halfIndex: null,
            playing: false
        },
        methods: {
            refreshScore: function () {
                axios.get(
                    '/online/api/state/' + matchId.toString()
                ).then(function (response) {
                    app.score = response.data.score;
                    app.halfIndex = response.data.halfIndex;
                    app.halfStart = response.data.halfStart;
                }).catch(function (error) {
                    console.log(error);
                });

            },
            refreshEvents: function () {
                axios.get(
                    '/online/api/events/' + matchId.toString()
                ).then(function (response) {
                    app.events = response.data.events;
                }).catch(function (error) {
                    console.log(error);
                });

            },
            refreshTime: function () {
                var secsFromStart = (new Date() - new Date(1000 * app.halfStart)) / 1000;
                if (secsFromStart < 10 * 60) {
                    app.time = new Date(secsFromStart * 1000);
                } else {
                    app.time = null;
                }

            },
            goal: function (playerId) {
                axios.post(
                    '/online/api/goal/' + matchId.toString(),
                    {
                        playerId: playerId
                    }
                ).then(function (response) {
                    app.refreshScore();
                    app.refreshEvents();
                    $('.modal').modal('hide');
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        computed: {
            timeFormatted: function () {
                if (!this.time) return '00:00';
                return this.time.getMinutes().pad(2) + ':' + this.time.getSeconds().pad(2);
            }
        },
        beforeMount: function () {
            this.refreshScore();
            this.refreshEvents();
            setInterval(function () {
                app.refreshScore();
            }, 5000);
            setInterval(function () {
                app.refreshTime();
            }, 500);
        }
    });


    Vue.filter('default', function (value, default_) {
        return (value == null || value === undefined) ? default_ : value;
    });
}