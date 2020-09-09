<template>
    <div class="card">
        <div class="card-header">{{ headingText }}</div>

        <div class="card-body">
            <form enctype="multipart/form-data" @submit.prevent.stop="save">
                <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputName" v-model="department.name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea
                            name="description"
                            id="inputDescription"
                            rows="3"
                            v-model="department.description"
                        >
                        </textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="custom-file">
                        <input type="file"
                               class="custom-file-input"
                               id="inputLogo"
                               @change="handleLogo"
                               accept="image/png, image/jpeg, image/gif"
                        >
                        <label class="custom-file-label" for="inputLogo" aria-describedby="inputGroupFileAddon02">
                            Choose file
                        </label>
                    </div>
                </div>
                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-2 pt-0">Radios</legend>
                        <div class="col-sm-10">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="option1" checked>
                                <label class="form-check-label" for="gridRadios1">
                                    First radio
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="option2">
                                <label class="form-check-label" for="gridRadios2">
                                    Second radio
                                </label>
                            </div>
                            <div class="form-check disabled">
                                <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="option3" disabled>
                                <label class="form-check-label" for="gridRadios3">
                                    Third disabled radio
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group row">
                    <div class="col-sm-2">Checkbox</div>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck1">
                            <label class="form-check-label" for="gridCheck1">
                                Example checkbox
                            </label>
                        </div>
                    </div>
                </div>
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
            id: 0,
            name: '',
            description: '',
            logo: ''
        };
    },
    methods: {
        getDepartment() {
            axios.get(this.indexUrl + '/' + this.id )
                .then(result => {
                    if (result.status === 200 && typeof result.data !== 'undefined') {
                        this.department = typeof result.data.department === 'object' ? result.data.department : {}
                        this.allUsers = typeof result.data.allUsers !== 'undefined' ? parseInt(result.data.allUsers) : []
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
            if (!this.id) {
            axios.post(this.indexUrl, formData)
                .then(response => this.handleResponse(response))
                .catch(errors => this.handleErrors(errors));
            } else {
                axios.patch(this.indexUrl + '/' + this.id, formData)
                .then(response => this.handleResponse(response))
                .catch(errors => this.handleErrors(errors));
            }
        },
        handleResponse(response) {
            //TODO
        },
        handleErrors(errors) {
            //TODO
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
            this.getDepartment()
        }
    }
}
</script>

<style scoped>
textarea {
    width: 100%;
}
</style>
