import Vue from 'vue'
import VueResource from 'vue-resource'
import Raven from 'raven-js'
import RavenVue from 'raven-js/plugins/vue'
import VueNativeSock from 'vue-native-websocket'
import VueLoading from 'vue-loading-template'


import 'vue2-animate/dist/vue2-animate.css';


Raven.config(
    'https://29dc267c2d6b4bcc80cfcb1ce1e34478@sentry.io/1205821',
    {
        ignoreUrls: [
            /.*127\..*/,
            /.*localhost.*/
        ]
    }
).addPlugin(RavenVue, Vue).install();

Vue.use(VueResource);

const config = window.config || {};


Number.prototype.pad = function (size, char = '0') {
    let sign = Math.sign(this) === -1 ? '-' : '';
    return sign + new Array(size).concat([Math.abs(this)]).join(char).slice(-size);
};

Vue.filter("onlineStateName", state => {
    return {
        'init': 'před zápasem',
        'half_first': '1. poločas',
        'pause': 'přestávka',
        'half_second': '2. poločas',
        'end': 'po zápase'
    }[state];
});

function installWebSocket(store) {
    try {
        Vue.use(VueNativeSock, config.liveServiceUrl, {
            reconnection: true,
            format: 'json',
            store
        });
        store.$socket = Vue.prototype.$socket;
    } catch (e) {
        console.warn('Failed to initialize WS due ' + e + '. Fallback is now used.');
        Raven.captureException(e);
    }
}

Vue.component('v-loading', {
    template: '<vue-loading type="spin" color="#0e5eff" :size="{ width: \'100px\', height: \'100px\' }"></vue-loading>',
    components: {VueLoading}
});

export default Vue;
export {Vue, installWebSocket};