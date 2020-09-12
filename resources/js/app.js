require('./bootstrap');

import VueRouter from 'vue-router';
window.Vue = require('vue');
window.Vue.use(VueRouter);
import router from './routes';
const axios = require('axios').default;
import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

const lang = document.querySelector('html').lang;
const i18n = new VueI18n({
    locale: lang,
});

Vue.component('app-component', require('./App').default);
Vue.component('departments-component', require('./components/DepartmentsComponent').default);
Vue.component('users-component', require('./components/UsersComponent').default);
Vue.component('departments-form-component', require('./components/DepartmentsFormComponent').default);

const app = new Vue({
    el: '#app',
    router,
    i18n,
    data: {
        lang: lang,
    },
    beforeCreate(){
        const messagesRu = require('../lang/ru.json')
        i18n.setLocaleMessage('ru', messagesRu);
    },
});
