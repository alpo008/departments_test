<template>
    <div class="card">
        <div class="card-header">
            <div class="page-title">{{ $t('Users') }}</div>
            <router-link :to="{name: 'addUser'}">
                <a class="btn btn-primary">{{ $t('Add') }}</a>
            </router-link>
        </div>
        <div class="card-body">
            <table class="table table-responsive" v-if="users.length">
                <tbody>
                <tr v-for="user in users">
                    <td>
                        {{ user.name }}
                    </td>
                    <td>
                        <a :href="'mailto:' + user.email">{{ user.email }}</a>
                    </td>
                    <td>
                        {{ user.updated_at | toLocale }}
                    </td>
                    <td>
                        <router-link :to="{name: 'editUser', params: {id: user.id}}">
                            <a class="btn btn-secondary">{{ $t('Update') }}</a>
                        </router-link>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-danger"
                            @click.prevent="deleteUser(user.id)"
                        >
                            {{ $t('Delete') }}
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <p v-else>
                No users data
            </p>
        </div>
        <div class="card-footer">
            <nav v-if="totalPages > 1">
                <ul class="pagination">
                    <li class="page-item"
                        v-for="i in totalPages"
                        :class="i === page ? 'active' : ''"
                    >
                        <a class="page-link" href="#" v-if="i !== page" @click.prevent="getUsers(i)">{{ i }}</a>
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
    name: 'Users',
    data() {
        return {
            indexUrl: '/user',
            users: [],
            page: 1,
            totalPages: 1
        };
    },
    methods: {
        getUsers(page) {
            axios.get(this.indexUrl, {
                params: {
                    page: page
                }
            })
                .then(result => {
                    if (result.status === 200 && typeof result.data !== 'undefined') {
                        this.users = typeof result.data.users === 'object' ? result.data.users : []
                        this.page = typeof result.data.page !== 'undefined' ? parseInt(result.data.page) : 1
                        this.totalPages = typeof result.data.totalPages !== 'undefined' ? result.data.totalPages : 1
                    }
                })
                .catch(error => console.error(error));
        },
        deleteUser(id) {
            if (confirm('Sure?')) {
                axios.delete(this.indexUrl + '/' + id)
                .then(result => {
                    if(result.data.code === 200) {
                        this.getUsers(this.page)
                    }
                })
                .catch(error => console.error(error))
            }
        }
    },
    filters: {
        toLocale(date) {
            let dateObject = new Date(date)
            return dateObject.toLocaleString()
        }

    },
    beforeMount() {
        this.getUsers(1)
    }
}
</script>

<style scoped>
.page-title {
    display: block;
    float: left;
    font-size: 1.5rem;
}
.card-header a.btn {
    float: right;
}
</style>
