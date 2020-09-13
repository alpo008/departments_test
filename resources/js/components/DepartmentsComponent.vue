<template>
    <div class="card">
        <div class="card-header">
            <div class="page-title">{{ $t('Departments') }}</div>
            <router-link :to="{name: 'addDepartment'}">
                <a class="btn btn-primary">{{ $t('Add') }}</a>
            </router-link>
        </div>
        <div class="card-body">
            <table class="table table-responsive" v-if="departments.length">
                <tbody>
                    <tr v-for="department in departments">
                        <td>
                            <img
                                width="80"
                                :src="imagePath(department.logo)"
                                :alt="department.name"
                                :title="department.name"
                            >
                        </td>
                        <td>
                            <p>{{ department.name }}</p>
                            <div class="description">{{ department.description }}</div>
                        </td>
                        <td>
                            <p v-if="department.users.length">{{ $t('Users') }}:</p>
                            <ol v-if="department.users.length">
                                <li v-for="user in department.users">
                                    {{ user.name }}
                                </li>
                            </ol>
                        </td>
                        <td>
                            <router-link :to="{name: 'editDepartment', params: {id: department.id}}">
                                <a class="btn btn-secondary">{{ $t('Update') }}</a>
                            </router-link>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-danger"
                                @click.prevent="deleteDepartment(department.id)"
                            >
                                {{ $t('Delete') }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else>
                No departments data
            </p>
        </div>
        <div class="card-footer">
            <nav v-if="totalPages > 1">
                <ul class="pagination">
                    <li class="page-item"
                        v-for="i in totalPages"
                        :class="i === page ? 'active' : ''"
                    >
                        <a class="page-link" href="#" v-if="i !== page" @click.prevent="getDepartments(i)">{{ i }}</a>
                        <span class="page-link" v-else>
                            {{ i }}
                            <span class="sr-only">(current)</span>
                        </span>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Departments',
    data() {
        return {
            indexUrl: '/department',
            departments: [],
            page: 1,
            totalPages: 1
        };
    },
    methods: {
        getDepartments(page) {
            axios.get(this.indexUrl, {
                params: {
                    page: page
                }
            })
            .then(result => {
                if (result.status === 200 && typeof result.data !== 'undefined') {
                    this.departments = typeof result.data.departments === 'object' ? result.data.departments : []
                    this.page = typeof result.data.page !== 'undefined' ? parseInt(result.data.page) : 1
                    this.totalPages = typeof result.data.totalPages !== 'undefined' ? result.data.totalPages : 1
                }
            })
            .catch(error => console.error(error));
        },
        imagePath(path) {
            let defaultImagePath = '/storage/logo/no-image.png';
            return !!path ? path : defaultImagePath
        },
        deleteDepartment(id) {
            if (confirm(this.$t('Sure?'))) {
                axios.delete(this.indexUrl + '/' + id)
                .then(result => {
                    if(result.data.code === 200) {
                        this.getDepartments(this.page)
                    }
                })
                .catch(error => console.error(error))
            }
        }
    },
    beforeMount() {
        this.getDepartments(1);
    }
}


</script>

<style scoped>
.card-header > button {
    float: right;
}
.page-title {
    display: block;
    float: left;
    font-size: 1.5rem;
}
.card-header a.btn {
    float: right;
}
.card-body {
    padding: 0.5rem;
}
table td {
    padding: 0.75rem;
    vertical-align: top!important;
}
table td:nth-child(1) {
    width: 80px;
    text-align: center;
}
table td:nth-child(3) {
    width: 35%;
}
table td p {
    font-weight: bolder;
    margin-bottom: 0!important;
}
.description {
    color: #4e555b;
    display: flex;
    flex-direction: row;
}
.pagination {
    margin-bottom: auto!important;
}
</style>
