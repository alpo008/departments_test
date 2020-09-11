import VueRouter from 'vue-router'

import Departments from './components/DepartmentsComponent.vue'
import Users from './components/UsersComponent.vue'
import DepartmentsForm from './components/DepartmentsFormComponent.vue'
import UsersForm from './components/UsersFormComponent.vue'


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
        {
            path: '/add-user',
            name: 'addUser',
            component: UsersForm,
        },
        {
            path: '/edit-user/:id',
            name: 'editUser',
            component: UsersForm,
        }
    ],
    mode: "history"
});
