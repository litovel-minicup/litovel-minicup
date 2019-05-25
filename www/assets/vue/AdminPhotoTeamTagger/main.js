import {Vue} from '../base/vue'

import App from './App.vue'

import store from './store/store'


const app = new Vue({
    el: '#admin-team-tagger',
    render: h => h(App),
    store,
});

