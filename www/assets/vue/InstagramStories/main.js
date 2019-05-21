import {Vue} from '../base/vue'

import App from './App.vue'

import store from './store/store'


const app = new Vue({
    el: '#instagram-stories',
    render: h => h(App),
    store,
});

