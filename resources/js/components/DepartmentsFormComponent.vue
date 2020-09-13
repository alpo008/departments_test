<template>
    <div class="card">
        <div class="card-header">{{ headingText }}</div>
        <div class="card-body">
            <form enctype="multipart/form-data" @submit.prevent.stop="save">
                <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">{{ $t('Name') }}</label>
                    <div class="col-sm-10">
                        <input type="text"
                               class="form-control"
                               :class="getError('name') ? 'is-invalid' : ''"
                               :placeholder="$t('Department name')"
                               id="inputName"
                               v-model="department.name"
                        >
                        <span role="alert" class="invalid-feedback" v-if="getError('name')">
                            <strong>{{ getError('name') }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputDescription" class="col-sm-2 col-form-label">{{ $t('Description') }}</label>
                    <div class="col-sm-10">
                        <textarea
                            name="description"
                            id="inputDescription"
                            :class="getError('description') ? 'is-invalid' : ''"
                            :placeholder="$t('Department description')"
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
                    <div class="col-sm-2">{{ $t('Logo') }}</div>
                    <div class="custom-file col-sm-10">
                        <input type="file"
                               class="custom-file-input"
                               :class="getError('mimeType') || getError('ext') || getError('size') ? 'is-invalid' : ''"
                               id="inputLogo"
                               @change="handleLogo"
                               accept="image/png, image/jpeg, image/gif"
                        >
                        <label class="custom-file-label" for="inputLogo" aria-describedby="inputGroupFileAddon02">
                            {{ $t('Choose logo') }}
                        </label>
                        <span role="alert"
                              class="invalid-feedback"
                              v-if="getError('mimeType') || getError('ext') || getError('size')">
                            <strong v-if="getError('mimeType')">{{ getError('mimeType') }}</strong> &nbsp;
                            <strong v-if="getError('ext')">{{ getError('ext') }}</strong> &nbsp;
                            <strong v-if="getError('size')">{{ getError('size') }}</strong>
                        </span>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-2 pt-0">{{ $t('Users') }}</legend>
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
                            {{ $t('Save') }}
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
            logoFile: {},
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
                let uploadedFile = e.target.files[0];
                if (typeof uploadedFile === 'object') {
                    this.logoFile.name = uploadedFile.name
                    this.logoFile.mimeType = uploadedFile.type
                    this.logoFile.size = Math.ceil(uploadedFile.size / 1000)
                    this.logoFile.ext = uploadedFile.name.substring(uploadedFile.name.indexOf(".")+1);
                    let fileReader = new FileReader()
                    fileReader.readAsDataURL(uploadedFile);
                    fileReader.onload = (event) => {
                        this.logoFile.body = event.target.result
                    };
                    fileReader.onerror = function() {
                        console.error(fileReader.error);
                    };
                }
            }
        },
        save() {
            let dataObject = {department: this.department, users: this.selectedUsers, logoFile: this.logoFile}
            if (this.id) {
                axios.patch(this.indexUrl + '/' + this.id, JSON.stringify(dataObject))
                    .then(response => this.handleResponse(response.data))
                    .catch(errors => this.handleErrors(errors));
            } else {
                axios.post(this.indexUrl, dataObject)
                    .then(response => this.handleResponse(response.data))
                    .catch(errors => this.handleErrors(errors));
            }
        },
        handleResponse(response) {
            if (response.code === 200) {
                this.errors = {}
                this.$router.push('/departments')
            } else {
                this.errors = response.errors;
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
                return this.$t('Add department')
            }
            return this.$t('Update department')
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
    .card-header {
        font-size: 1.5rem;
    }
    textarea {
        width: 100%;
    }
    fieldset {
        padding: 1rem 0
    }
    .custom-file-input {
        cursor: pointer;
    }
</style>
