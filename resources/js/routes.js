import VueRouter from 'vue-router'

import Departments from './components/DepartmentsComponent.vue'
import Users from './components/UsersComponent.vue'
import DepartmentsForm from './components/DepartmentsFormComponent.vue'


export default new VueRouter({
    routes: [
        {
            path: '/departments',
            name: 'departments',
            component: Departments,
        },
        {
            path: '/users',
            name: 'users',
            component: Users
        },
        {
            path: '/add-department',
            name: 'addDepartment',
            component: DepartmentsForm,
        },
        {
            path: '/edit-department/:id',
            name: 'editDepartment',
            component: DepartmentsForm,
        },
    ],
    mode: "history"
});
