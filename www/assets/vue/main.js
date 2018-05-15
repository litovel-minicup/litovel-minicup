import Vue from 'vue'
import VueResource from 'vue-resource'
import Raven from 'raven-js'
import RavenVue from 'raven-js/plugins/vue'
import store from './store/store'
import VueNativeSock from 'vue-native-websocket'
import App from './App.vue'

Raven.config(
    'https://29dc267c2d6b4bcc80cfcb1ce1e34478@sentry.io/1205821',
    {
        ignoreUrls: ['localhost:8000', '127.0.0.1']
    }
).addPlugin(RavenVue, Vue).install();

Vue.use(VueResource);

const config = window.config || {};

Vue.use(VueNativeSock, config.liveServiceUrl, {
    reconnection: true,
    format: 'json',
    store
});
store.$socket = Vue.prototype.$socket;


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

const app = new Vue({
    el: '#app',
    render: h => h(App),
    store,
});

