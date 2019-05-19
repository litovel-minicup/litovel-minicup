import {installWebSocket, Vue} from '../base/vue'

import App from './App.vue'

import store from './store/store'

installWebSocket(store);

const app = new Vue({
    el: '#live-bar',
    render: h => h(App),
    store,
});

