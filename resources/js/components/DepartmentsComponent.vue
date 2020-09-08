<template>
    <div class="card">
        <div class="card-header">
            <div class="page-title">Departments</div>
            <button type="button" class="btn btn-primary">Add</button>
        </div>
        <div class="card-body">
            <table class="table-responsive" v-if="departments.length">
                <tbody>
                    <tr v-for="department in departments">
                        <td>
                            <img
                                width="60"
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
                            <p v-if="department.users.length"> Users: </p>
                            <ol v-if="department.users.length">
                                <li v-for="user in department.users">
                                    {{ user.name }}
                                </li>
                            </ol>
                        </td>
                        <td>
                            <button type="button" class="btn btn-secondary">Update</button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger">Delete</button>
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
                    console.log(result.data)
                    this.departments = typeof result.data.departments === 'object' ? result.data.departments : []
                    this.page = typeof result.data.page !== 'undefined' ? parseInt(result.data.page) : 1
                    this.totalPages = typeof result.data.totalPages !== 'undefined' ? result.data.totalPages : 1
                    console.log(this.totalPages)
                }
            })
            .catch(error => console.error(error));
        },
        imagePath(path) {
            let defaultImagePath = '/img/logo/no-image.png';
            return !!path ? path : defaultImagePath
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
.card-body {
    padding: 0.5rem;
}
table td {
    padding: 0.75rem;
    vertical-align: center!important;
}
table td:nth-child(1) {
    width: 80px;
    text-align: center;
}
table td:nth-child(3) {
    width: 20%;
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
