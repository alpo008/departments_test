require('./bootstrap');

import VueRouter from 'vue-router';
window.Vue = require('vue');
window.Vue.use(VueRouter);
import router from './routes';


const axios = require('axios').default;


Vue.component('app-component', require('./App').default);
Vue.component('departments-component', require('./components/DepartmentsComponent').default);
Vue.component('users-component', require('./components/UsersComponent').default);
Vue.component('departments-form-component', require('./components/DepartmentsFormComponent').default);

const app = new Vue({
    el: '#app',
    router
});
