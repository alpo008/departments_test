<template>
    <div class="card">
        <div class="card-header">{{ headingText }}</div>
        <div class="card-body">
            <form enctype="multipart/form-data" @submit.prevent.stop="save">
                <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text"
                               class="form-control"
                               :class="getError('name') ? 'is-invalid' : ''"
                               id="inputName"
                               v-model="department.name"
                        >
                        <span role="alert" class="invalid-feedback" v-if="getError('name')">
                            <strong>{{ getError('name') }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea
                            name="description"
                            id="inputDescription"
                            :class="getError('description') ? 'is-invalid' : ''"
                            rows="3"
                            v-model="department.description"
                        >
                        </textarea>
                        <span role="alert" class="invalid-feedback" v-if="getError('description')">
                            <strong>{{ getError('description') }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="custom-file">
                        <input type="file"
                               class="custom-file-input"
                               :class="getError('logo') ? 'is-invalid' : ''"
                               id="inputLogo"
                               @change="handleLogo"
                               accept="image/png, image/jpeg, image/gif"
                        >
                        <label class="custom-file-label" for="inputLogo" aria-describedby="inputGroupFileAddon02">
                            Choose file
                        </label>
                        <span role="alert" class="invalid-feedback" v-if="getError('logo')">
                            <strong>{{ getError('logo') }}</strong>
                        </span>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-2 pt-0">Users</legend>
                        <div class="col-sm-10">
                            <div class="form-check" v-for="user in allUsers">
                                <input class="form-check-input"
                                       type="checkbox" name="users"
                                       :id="'users_' + user.id"
                                       value="1"
                                       :checked="selectedUsers.indexOf(user.id) !== -1"
                                        @change="toggleUser(user.id)"
                                >
                                <label class="form-check-label" :for="'users_' + user.id">
                                    {{ user.name }} ( <a :href="'mailto:' + user.email">{{ user.email }}</a> )
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "DepartmentsFormComponent",
    data() {
        return {
            indexUrl: '/department',
            department: {},
            allUsers: [],
            selectedUsers: [],
            id: 0,
            logo: '',
            errors: {}
        };
    },
    methods: {
        getDepartment() {
            axios.get(this.indexUrl + '/' + this.id )
                .then(result => {
                    if (result.status === 200 && typeof result.data !== 'undefined') {
                        this.department = typeof result.data.department === 'object' &&
                            !jQuery.isEmptyObject(result.data.department) ?
                            result.data.department : {}
                        this.allUsers = typeof result.data.allUsers !== 'undefined' ? result.data.allUsers : []
                        if (typeof this.department.users === 'object' && this.department.users.length) {
                            this.department.users.forEach(user => {
                                this.selectedUsers.push(user.id)
                            })
                        }
                    }
                })
                .catch(errors => this.handleErrors(errors));
        },
        handleLogo(e) {
            if (e.target.files.length) {
                this.logo = e.target.files[0];
            }
        },
        save() {
            let formData = new FormData();
            formData.append('logo', this.logo);
            formData.append('department', JSON.stringify(this.department));
            formData.append('users', JSON.stringify(this.selectedUsers));
            if (!this.id) {
            axios.post(this.indexUrl, formData)
                .then(response => this.handleResponse(response.data))
                .catch(errors => this.handleErrors(errors));
            } else {
                axios.patch(this.indexUrl + '/' + this.id, formData)
                .then(response => this.handleResponse(response))
                .catch(errors => this.handleErrors(errors));
            }
        },
        handleResponse(response) {
            if (response.code === 200) {
                this.errors = {}
                this.$router.push('/departments')
            } else {
                this.errors = response.data;
            }
        },
        handleErrors(errors) {
            alert(errors.toString())
        },
        getError(name) {
            let errors = this.errors[name]
            if (typeof errors === 'string') {
                return errors
            }
            if (typeof errors === 'object' && typeof errors[0] === 'string') {
                return errors[0]
            }
            return false;
        },
        toggleUser(id) {
            let index = this.selectedUsers.indexOf(id);
            if (index === -1) {
                this.selectedUsers.push(id)
            } else {
                this.selectedUsers.splice(index, 1)
            }
        }
    },
    computed: {
        headingText() {
            if (!this.id) {
                return 'Add department'
            }
            return 'Update department'
        }
    },
    beforeMount() {
        let id = parseInt(this.$route.params.id);
        if (!isNaN(id)) {
            this.id = id;
        }
        this.getDepartment()
    }
}
</script>

<style scoped>
textarea {
    width: 100%;
}
</style>
